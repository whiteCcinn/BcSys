<?php

namespace Bc\Lib\Coroutine;

/**
 * Class RetVal
 *
 * @author      caiwh<471113744@qq.com>
 * @version     1.0.0
 */
class RetVal
{

  public $info;

  public function __construct($info)
  {
    $this->info = $info;
  }
}