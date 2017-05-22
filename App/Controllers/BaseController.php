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
   *
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

  /**
   * 输出视图文件
   *
   * @param null $ViewName
   * @param null $Param
   * @param bool $Return
   *
   * @return null|string
   */
  public function display($ViewName = null, $Param = null, $Return = false)
  {
    $Modules = $Module = '';
    if (is_array($ViewName) || $ViewName == null)
    {
      $Param = $ViewName;

      $BackTrace = debug_backtrace();
      next($BackTrace);
      $BackTrace      = current($BackTrace);
      $ViewName       = substr($BackTrace['function'], 0, -strlen('Action'));
      $ControllerName = explode('\\', $BackTrace['class']);
      $Modules        = $ControllerName[2];
      $Module         = $ControllerName[3];
      $ControllerName = array_pop($ControllerName);
      $ControllerName = substr($ControllerName, 0, -strlen('Controller'));
      $ViewName       = $ControllerName . DIRECTORY_SEPARATOR . $ViewName;
    }
    if (!is_null($Param) && is_array($Param))
    {
      extract($Param);
    }

    if ($Return)
    {
      ob_start();
    }

    require implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), $Modules, $Module, 'Views', $ViewName . '.php']);

    if ($Return)
    {
      $view = ob_get_contents();

      ob_end_clean();
    }

    return isset($view) ? $view : null;
  }
}