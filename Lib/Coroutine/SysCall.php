<?php
namespace Bc\Lib\Coroutine;

/**
 * 系统调用
 * Class SysCall
 *
 * @author      caiwh<471113744@qq.com>
 * @version     1.0.0
 */
class SysCall
{

  public static function end($words)
  {
    return new RetVal($words);
  }
}