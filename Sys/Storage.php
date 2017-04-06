<?php
/**
 * Created by PhpStorm.
 * User: Cwh-Macbook
 * Date: 2017/3/4
 * Time: 00:36
 */

namespace Bc\Sys;


/**
 * 存储变量桶
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class Storage
{
  /**
   * 请求
   * @var \Swoole\Http\Request
   */
  private static $request = null;

  /**
   * 响应
   * @var \Swoole\Http\Response
   */
  private static $response = null;

  /**
   * HttpServer
   * @var \Swoole\Http\Server
   */
  private static $http = null;

  /**
   * 协程任务调度器
   * @var \Bc\Lib\Coroutine\Scheduler
   */
  private static $scheduler = null;

  private static $vars = [];

  /**
   * mysql连接池指向的当前连接数
   * @var null
   */
  private static $current_connects = null;

  /**
   * redis连接池指向的当前连接数
   * @var null
   */
  private static $redis_current_connects = null;

  /**
   * 设置属性的值
   * @param $name
   * @param $value
   *
   * @return bool
   */
  public static function _set($name, $value)
  {
    self::_init_();

    if (in_array($name, self::$vars))
    {
      self::$$name = $value;

      return true;
    } else
    {
      return false;
    }
  }


  /**
   * 初始化
   */
  private static function _init_()
  {
    if (empty(self::$vars))
    {
      self::$vars = array_keys(get_class_vars(self::class));
      unset(self::$vars['vars']);
    }
  }

  /**
   * 获取属性
   * @param string $name
   *
   * @return bool
   */
  public static function _get($name)
  {
    self::_init_();

    if (in_array($name, self::$vars))
    {
      return self::$$name;
    } else
    {
      return false;
    }

  }

}