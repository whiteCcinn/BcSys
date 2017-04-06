<?php
namespace Bc\Sys\Ioc;

use Bc\Exception\RunTimeException;

/**
 * Ioc容器
 *
 * Ioc容器，实现依赖注入
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class Di
{

  private static $instance = null;

  public static function getInstance()
  {
    if (!self::$instance instanceof self)
    {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * setter注入
   *
   * @param string $var1 要注入的对象
   * @param mixed  $var2 调用方式
   *                     <li> className = 'namespace\class' 带命名空间带类 </li>
   *                     <li> parameters array 参数数组 </li>
   *                     <li>  parameter array 对象初始化参数  </li>
   *                     ....
   *
   * @return bool
   */
  public function set(string $var1, $var2)
  {
    if (empty($var1) || empty($var2))
    {
      return false;
    }

    $this->$var1 = $var2;
  }


  /**
   * 从Di容器获取对象
   *
   * @param $varName
   *
   * @single 是否单例
   * @return bool
   */
  public function get($varName, $single = false, $multi = false)
  {
    if (isset($this->$varName))
    {
      if (is_object($this->$varName) && $single)
      {
        return $this->$varName;
      }

      if (is_string($this->$varName))
      {
        if($multi)
        {
          return new $this->$varName;
        }

        $this->$varName = new $this->$varName;

      } elseif (is_array($this->$varName))
      {
        if (!isset($this->$varName['className']) || !isset($this->$varName['parameters']))
        {
          return false;
        }

        $className  = $this->$varName['className'];
        $parameters = isset($this->$varName['parameters']) && !empty($this->$varName['parameters']) ? $this->$varName['parameters'] : [];

        if($multi)
        {
          return new $className($parameters['parameter']);
        }

        $this->$varName = new $className($parameters['parameter']);
      } elseif ($varName instanceof \Closure)
      {
        $this->$varName = ($varName)();

      }

      return $this->$varName;
    }

    return false;
  }


  /**
   * 支持$Di->getDb()；
   *
   * @param $name
   * @param $arguments
   *
   * @return bool
   */
  public function __call($name, $arguments)
  {
    $varName = strtolower(substr($name, 3));

    return call_user_func([$this, 'get'], $varName);
  }
}