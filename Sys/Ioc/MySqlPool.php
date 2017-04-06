<?php

namespace Bc\Sys\Ioc;

use Bc\Sys\Io\MysqlPdo;
use Bc\Sys\Storage;

/**
 * MySql连接池
 *
 * MySql连接池操作类，减少频繁的IO操作
 * 支持多数据库
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class MySqlPool
{
  public static $last_db = null;

  public static $pool = [];

  private static $config = [];

  private static $di = null;

  private function __construct($config = [])
  {
  }

  private function __clone()
  {
    // TODO: Implement __clone() method.
  }

  /**
   * 出队列
   *
   * @return mixed
   */
  public static function pop($db = null)
  {
    if ($db == null)
    {
      $current_connects = Storage::_get('current_connects');

      $db = key(self::$pool);

      if (count(current(self::$pool)) <= 0 && $current_connects[ $db ] < self::$config['max_connects'])
      {
        current(self::$pool)->enqueue(self::$di->get('MySqlPool:' . $db, false, true));

        $current_connects[ $db ] += 1;

        Storage::_set('current_connects', $current_connects);

      } elseif (count(current(self::$pool)) <= 0 && $current_connects[ $db ] >= self::$config['max_connects'])
      {
        // 该逻辑之后已经无用，释放DI容器
        while (true)
        {
          if (count(current(self::$pool)) > 0)
            break;
        }

//        return null;
      }

      unset($current_connects);

      return current(self::$pool)->dequeue();
    } else
    {
      $current_connects = Storage::_get('current_connects');

      if (count(self::$pool[ $db ]) <= 0 && $current_connects[ $db ] < self::$config['max_connects'])
      {
        self::$pool[ $db ]->enqueue(self::$di->get('MySqlPool:' . $db, false, true));

        $current_connects[ $db ] += 1;

        Storage::_set('current_connects', $current_connects);

        unset($current_connects);

      } elseif (count(self::$pool[ $db ]) <= 0 && $current_connects[ $db ] >= self::$config['max_connects'])
      {

        // 该逻辑之后已经无用，释放DI容器
        while (count(self::$pool[ $db ]) > 0)
        {
          break;
        }

//        return null;
      }

      unset($current_connects);

      return self::$pool[ $db ]->dequeue();
    }
  }

  /**
   * 入队列
   *
   * @param $mysql 数据库连接对象
   *
   * @return bool
   */
  public static function push($mysql, $db = null)
  {
    if ($db == null)
    {
      current(self::$pool)->enqueue($mysql);
    }
    else
    {
      self::$pool[ $db ]->enqueue($mysql);
    }

    return true;
  }

  /**
   * 进程初始化连接池队列
   *
   * @param       $di
   * @param array $config
   *
   * @return bool
   */
  public static function run($di, $config = [])
  {
    $config = self::$config = array_merge(self::$config, $config);

    self::$di = $di;

    if (!$config['pool'])
    {
      return false;
    }

    $databases = explode(',', $config['database']);
    for ($i = 0; $i < count($databases); $i++)
    {
      self::$last_db                = $databases[ $i ];
      self::$pool[ self::$last_db ] = new \SplQueue();

      $di->set('MySqlPool:' . self::$last_db, [
          'className'  => MysqlPdo::class,
          'parameters' => [
              'parameter' =>
                  array_merge($config, ['database' => $databases[ $i ]])
          ]
      ]);

      while (count(self::$pool[ self::$last_db ]) < $config['connects'])
      {
        $mysql_instance = $di->get('MySqlPool:' . self::$last_db, false, true);
        self::$pool[ self::$last_db ]->enqueue($mysql_instance);
      }

      $current_connects = Storage::_get('current_connects');

      !is_array($current_connects) && $current_connects = [];

      $current_connects[ self::$last_db ] = $config['connects'];

      Storage::_set("current_connects", $current_connects);
    }

    // 常驻进程，手动释放变量内存
    unset($mysql_instance, $databases, $config, $di, $current_connects);

    return true;
  }
}