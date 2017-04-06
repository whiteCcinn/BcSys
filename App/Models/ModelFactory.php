<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/1
 * Time: 17:47
 */

namespace Bc\App\Models;


/**
 * model工厂,暂时无用
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class ModelFactory
{
  public function __invoke($className,$function)
  {
    new $className;
  }

}