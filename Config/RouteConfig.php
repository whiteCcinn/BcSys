<?php
namespace Bc\Config;

/**
 * 初始化路由配置
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class RouteConfig
{
  /**
   * [
   *    'getArticleList'=>[
   *      'module'=>'',
   *      'controller'=>'',
   *      'action'=>''
   *     ],
   *   ...
   * ]
   *
   * @var array
   */
  public static $router_list = [];

  public static function addRoute(array $route)
  {
    return array_push(self::$router_list, $route);
  }

  public static function exists(string $route)
  {
    return array_key_exists($route, self::$router_list);
  }

  public static function getRouteInfo(string $route)
  {
    if (!self::exists($route))
    {
      return [];
    }

    return self::$router_list[ $route ];
  }

  public static function getRoute(string $route)
  {
    $routerInfo = self::getRouteInfo($route);
    if (empty($route))
    {
      return [];
    }

    $string = [
        'Bc\App\Modules',
        ucfirst($routerInfo['module']),
        'Controllers\\' . ucfirst($routerInfo['controller']) . 'Controller'
    ];

    return ['className' => implode('\\', $string), 'action' => $routerInfo['action'] . 'Action', 'isCoroutine' => (isset($routerInfo['coroutine']) && $routerInfo['coroutine'] === true) ? true : false];
  }

  public static function initRoute()
  {
    if (empty(self::$router_list))
    {
      self::$router_list = require_once __DIR__ . DIRECTORY_SEPARATOR . 'Static' . DIRECTORY_SEPARATOR . 'Route.ini.php';
    }
  }
}