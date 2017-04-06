<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/22
 * Time: 10:26
 */

namespace Bc\Sys\Ioc;


use Bc\Sys\Io\Redis;
use Bc\Sys\Storage;

/**
 * Redis连接池
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class RedisPool
{

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
  public static function pop()
  {
    $current_connects = Storage::_get('redis_current_connects');

    if (count(self::$pool) <= 0 && $current_connects < self::$config['max_connects'])
    {
      self::$pool->enqueue(self::$di->get('Redis', false, true));

      $current_connects += 1;

      Storage::_set('redis_current_connects', $current_connects);

      unset($current_connects);

    } elseif (count(self::$pool) <=0 && $current_connects >= self::$config['max_connects'])
    {

      // 该逻辑之后已经无用，释放DI容器
      while(count(self::$pool) > 0)
      {
        break;
      }

//      return null;
    }

    unset($current_connects);

    return self::$pool->dequeue();
  }

  /**
   * 入队列
   *
   * @param $redis Redis连接对象
   *
   * @return bool
   */
  public static function push($redis)
  {

    self::$pool->enqueue($redis);

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

    self::$pool = new \SplQueue();

    $di->set('Redis', [
        'className'  => Redis::class,
        'parameters' => [
            'parameter' => $config
        ]
    ]);

    while (count(self::$pool) < $config['connects'])
    {
      $redis_instance = $di->get('Redis', false, true);

      self::$pool->enqueue($redis_instance);
    }

    $current_connects = Storage::_get('redis_current_connects');

    !is_array($current_connects) && $current_connects = [];

    $current_connects = $config['connects'];

    Storage::_set("redis_current_connects", $current_connects);


    // 常驻进程，手动释放变量内存
    unset($redis_instance, $config, $di, $current_connects);

    return true;
  }
}