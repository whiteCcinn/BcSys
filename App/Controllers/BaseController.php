<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/21
 * Time: 10:07
 */

namespace Bc\App\Controllers;

/**
 * 抽象基本控制器
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
abstract class BaseController
{
  // 映射model的数组
  protected $_modelMap;

  /**
   * 回调生成model,防Model连接池连接泄漏
   * @param $ModelName
   * @param $function
   *
   * @return null
   */
  public function GenModel($ModelName, $function)
  {
    // 映射model
    $model = new $this->_modelMap[$ModelName];

    // 主体业务回调函数
    $data = $function($model);

    // 调用回收连接函数
    $model->close();

    return !empty($data) ? $data : null;
  }
}