<?php
namespace Bc\App;

use Bc\Config\InitConfig;
use Bc\Config\RouteConfig;
use Bc\Lib\Coroutine\Scheduler;
use Bc\Sys\Response\Response;
use Bc\Sys\Storage;

/**
 * 引导器
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class Bootstrap
{
  public static $instance = null;

  private function __construct()
  {
    spl_autoload_register([$this, 'my_autoload'], false, false);
  }

  public static function bootstrap()
  {
    date_default_timezone_set('PRC');

    Bootstrap::getInstance();

    Bootstrap::initObject();

    Bootstrap::initSchedule();
  }

  public static function run()
  {
    $request = Storage::_get('request');
    $param   = isset($request->get) ? $request->get : (isset($request->post) ? $request->post : []);

    $rInfo = [
        'className' => '',
        'action'    => ''
    ];

    if (RouteConfig::exists($route = trim($request->server['request_uri'], '\/')))
    {
      $rInfo = RouteConfig::getRoute($route);
      $response = Storage::_get('response');

      $response->header("Content-Type", "{$rInfo['type']};charset=utf-8");

      $className = new $rInfo['className'];
      $action    = $rInfo['action'];

      if (!$rInfo['isCoroutine'])
      {
        // 非协程调用
        \call_user_func([$className, $action], $param);
      } else
      {
        // 协程调用
        $scheduler = Storage::_get('scheduler');
        $scheduler->newTask($className->$action($param));
        $scheduler->run();
      }
    } else
    {
      Response::Out(['code' => -7000, 'msg' => 'Please Check the Router-List!']);
    }
  }

  /**
   * 获取对象
   *
   * @return null | $this
   */
  public static function getInstance()
  {
    if (static::$instance == null)
    {
      static::$instance = new self();
    }

    return static::$instance;
  }

  /**
   * 初始化各种对象
   */
  public static function initObject()
  {
    $initConfig = new InitConfig();
    $initConfig->configRun();
    $initConfig->PoolRun();

    RouteConfig::initRoute();
  }

  /**
   * 初始化协程任务调度器
   */
  public static function initSchedule()
  {
    $scheduler = new Scheduler();
    Storage::_set('scheduler', $scheduler);
  }

  /**
   * Auto loading the file of php
   *
   * @param $className
   */
  private function my_autoload($className)
  {
    $prefix       = \BcIndexConstant\PREFIX_NAMESPACE . '\\';
    $prefixLength = strlen($prefix);
    $file         = $className . \BcIndexConstant\EXC_FILE;

    if (0 === strpos($className, $prefix))
    {
      $file = explode('\\', substr($className, $prefixLength));
      $file = implode(DIRECTORY_SEPARATOR, $file) . \BcIndexConstant\EXC_FILE;
    }

    $path = \BcIndexConstant\ROOT_PATH . $file;

//        echo PHP_EOL.'=====================';
//        echo $path;
//        echo '====================='.PHP_EOL;
    if (file_exists($path))
    {
      require_once $path;
    }

  }


}