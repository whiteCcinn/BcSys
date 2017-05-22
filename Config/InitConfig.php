<?php

namespace Bc\Config;

use Bc\Sys\AsyncIO\AsyncMysql;
use Bc\Sys\Io\Log;
use Bc\Sys\Ioc\Di;
use Bc\Sys\Ioc\MySqlPool;
use Bc\Sys\Ioc\RedisPool;

/**
 * 初始化各种配置
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class InitConfig
{
  private $_config = [];

  private $_di = null;

  public function __construct()
  {
    $this->_loadFile();
  }

  public function configRun()
  {
    $this->_di->set('AsyncMySql', [
        'className'  => AsyncMysql::class,
        'parameters' => [
            'parameter' =>
                $this->_config['MYSQL']
        ]
    ]);

//
//    $this->_di->set('MySql', [
//        'className'  => MysqlPdo::class,
//        'parameters' => [
//            'parameter' =>
//                $this->_config['MYSQL']
//        ]
//    ]);

    $this->_di->set('MySqlLog', Log::class);
  }

  public function PoolRun()
  {
    # MysqlPool
    if ($this->_config['MYSQL']['status'] == 'true')
    {
      MySqlPool::run($this->_di, $this->_config['MYSQL']);

      // Mysql连接池的连接句柄的心跳包，Mysql默认8小时无请求gone away，每7小时跳一下
      \Swoole\Timer::tick(25200 * 1000, function ()
      {
        foreach (MySqlPool::$pool as $pool)
        {
          $tempMysql = [];
          $mysql     = null;

          while (!$pool->isEmpty())
          {
            $mysql = $pool->dequeue();

            // 心跳
            $mysql->query("SELECT 1 AS heartbeat");

            $msg = '[ ' . date('Y-m-d H:i:s') . ' ] : ' . '~心跳~Pong' . PHP_EOL;

            file_put_contents(dirname(__FILE__, 2) . '/logs/mysql/heartbeat.log', $msg, FILE_APPEND);

            $tempMysql[] = $mysql;
          }

          foreach ($tempMysql as $mysql)
          {
            $pool->enqueue($mysql);
          }

          unset($tempMysql, $mysql);
        }
      }, MySqlPool::$pool);

    }
    # RedisPool
    if ($this->_config['REDIS']['status'] == 'true')
    {
      RedisPool::run($this->_di, $this->_config['REDIS']);
    }
  }

  private function _loadFile()
  {
    $this->_di     = Di::getInstance();
    $this->_config = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . 'Static' . DIRECTORY_SEPARATOR . 'Settings.ini', true);
  }
}