<?php

namespace Bc\Sys\Server;

use Bc\App\Bootstrap;
use Bc\Sys\Response\Response;
use Bc\Sys\Storage;

/**
 * 主服务
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class SolServ
{
  public $http;
  public static $instance;

  /**
   * 初始化
   */
  private function __construct()
  {
    $http = $this->http = new \Swoole\Http\Server("0.0.0.0", 9501);

    $http->set(
        [
            'worker_num'    => 3,        //worker进程数量
            'daemonize'     => true,    //守护进程设置成true
            'max_request'   => 10000,    //最大请求次数，当请求大于它时，将会自动重启该worker
            'dispatch_mode' => 1,
            'log_file'      => '/path/to/BcSys/logs/server/server.log',
        ]
    );

    $http->on('WorkerStart', [$this, 'onWorkerStart']);
    $http->on('ManagerStart', [$this, 'onManagerStart']);
    $http->on('request', [$this, 'onRequest']);
    $http->on('start', [$this, 'onStart']);
    $http->on('close', [$this, 'onClose']);
    $http->on('WorkerError', [$this, 'onWorkerError']);
    $http->on('WorkerError', [$this, 'onWorkerError']);
    $http->on('WorkerStop', [$this, 'onWorkerStop']);
    $http->start();
  }

  /**
   * @param \Swoole\Server $serv
   *
   * @return bool
   */
  public function onStart(\Swoole\Server $serv)
  {
    $time = gmdate("Y-m-d H:i:s", time() + 8 * 60 * 60);
    $msg  = "->[onStart] PHP=" . PHP_VERSION . " swoole=" . SWOOLE_VERSION . " Master-Pid={$this->http->master_pid} Manager-Pid={$this->http->manager_pid}" . ' time=' . $time . PHP_EOL;
    $this->_write_file(dirname(__DIR__, 2) . '/logs/server/StdOutServer.log', $msg);
    swoole_set_process_name("php-wcapplet:master");

    return true;
  }

  public function onClose(\Swoole\Server $serv, int $work_id)
  {
    // 回收对应进程申请的资源
    $time = gmdate("Y-m-d H:i:s", time() + 8 * 60 * 60);
    $msg  = "->[onClose] time=" . $time . PHP_EOL;
    $this->_write_file(dirname(__DIR__, 2) . '/logs/server/StdOutServer.log', $msg);
  }

  /**
   * ManagerProcessOnStart
   *
   * @param \Swoole\Server $serv
   */
  public function onManagerStart(\Swoole\Server $serv)
  {
    $time = gmdate("Y-m-d H:i:s", time() + 8 * 60 * 60);
    $msg  = "->[onManagerStart] time=" . $time . PHP_EOL;
    $this->_write_file(dirname(__DIR__, 2) . '/logs/server/StdOutServer.log', $msg);
    swoole_set_process_name("php-wcapplet:manager");
  }

  /**
   * manager进程结束的时候调用
   *
   * @param \Swoole\Server $serv
   *
   * @return bool
   */
  public function onManagerStop(\Swoole\Server $serv)
  {
    $time = gmdate("Y-m-d H:i:s", time() + 8 * 60 * 60);
    $msg  = "->[onManagerStop] time=" . $time . PHP_EOL;
    $this->_write_file(dirname(__DIR__, 2) . '/logs/server/StdOutServer.log', $msg);

    return true;
  }

  /**
   * worker start时调用
   *
   * @param \Swoole\Server $serv
   * @param int            $worker_id
   */
  public function onWorkerStart(\Swoole\Server $serv, int $worker_id)
  {

    date_default_timezone_set('PRC');

    if ($serv->taskworker)
    {
      swoole_set_process_name("php-wcapplet[{$worker_id}] : task" . 'er');
    } else
    {
      swoole_set_process_name("php-wcapplet[{$worker_id}]: worker");
    }

    /**
     * TODO : use define APPLICATION_PATH
     */
    defined('APPLICATION_PATH') or define('APPLICATION_PATH', dirname(__DIR__, 2));

    include APPLICATION_PATH . '/index.php';

    include APPLICATION_PATH . '/Sys/Storage.php';

    register_shutdown_function([$this, 'shutdown']);

    Storage::_set('http', $this->http);

    \Bc\Index::run();

  }

  /**
   * worker/tasker进程结束的时候调用
   * 在此函数中可以回收worker进程申请的各类资源
   *
   * @param \Swoole\Server $serv
   * @param int            $work_id
   *
   * @return bool
   */
  public function onWorkerStop(\Swoole\Server $serv, int $work_id)
  {
    $time = gmdate("Y-m-d H:i:s", time() + 8 * 60 * 60);
    $msg  = "->[onWorkerStop] work_id" . $work_id . ' time=' . $time . PHP_EOL;
    $this->_write_file(dirname(__DIR__, 2) . '/logs/server/StdOutServer.log', $msg);

    return true;
  }

  public function onWorkerError(\Swoole\Server $serv, int $work_id, int $work_pid, int $exit_code)
  {
    $time = gmdate("Y-m-d H:i:s", time() + 8 * 60 * 60);
    $msg  = "->[onWorkerError] work_id=" . $work_id . ",work_pid=" . $work_pid . ",exit_code=" . $exit_code . ' time=' . $time . PHP_EOL;
    $this->_write_file(dirname(__DIR__, 2) . '/logs/server/StdOutServer.log', $msg);

    return true;
  }

  /**
   * 当request时调用
   *
   * @param unknown $request
   * @param unknown $response
   */
  public function onRequest($request, $response)
  {
    $response->header("Content-Type", "application/json;charset=utf-8");

    Storage::_set('request', $request);
    Storage::_set('response', $response);

    try
    {

      ob_start();

      Bootstrap::run();

      $result = ob_get_contents();

      ob_end_clean();

      $result = empty($result) ? 'No message' : $result;

      $this->flushOut($result);

    } catch (\Exception $e)
    {
      $response->end($e->getMessage());
    }
  }

  public static function getInstance()
  {

    if (!self::$instance)
    {
      self::$instance = new self();
    }

    return self::$instance;
  }


  /**
   * 由于异步IO只能在worker进程使用，并且异步文件IO目前只是实现性质，所以还是采用了原生的PHP写法
   *
   * @param string $filename 文件名
   * @param string $msg 消息
   * @param int    $flag 操作类型
   *
   * @return int
   */
  private function _write_file($filename, $msg, $flag = FILE_APPEND)
  {
    $ret = file_put_contents($filename, $msg, $flag);

    return $ret;
  }

  /**
   * 处理Server异常
   */
  private function shutdown()
  {
    $response = Storage::_get('response');

    if (ob_get_level() > 0)
    {

      Response::Out(['code' => -5000, 'msg' => 'Server Error,Please call us ** Team!']);

      $out = ob_get_contents();

    } else
    {
      ob_start();

      Response::Out(['code' => -5000, 'msg' => 'Server Error,Please call us ** Team!']);

      $out = ob_get_contents();

      ob_end_clean();
    }

    $this->flushOut($out);
  }

  /**
   * 响应客户端
   *
   * @param $result
   */
  private function flushOut($result)
  {

    $response = Storage::_get('response');
    $http     = Storage::_get('http');

    if (($http->connection_info($response->fd) !== false))
      if (($http->connection_info($response->fd) !== false))
      {
        /**
         * 如果没有response成功的话 , 通知监听服务器reload框架主进程
         */
        if (!$response->end($result))
        {
          $this->http->reload();
        }
      }
  }
}

SolServ::getInstance();