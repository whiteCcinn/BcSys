<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/20
 * Time: 16:57
 */

namespace Bc\Sys\Response;

/**
 * 响应请求
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class Response
{
  private $type = 'json';

  public static function Out($data = [])
  {
    $data = array_filter($data);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    return true;
  }
}