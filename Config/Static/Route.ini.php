<?php
/**
 * 路由配置文件
 *
 * @支持
 *   [module]      *
 *   [controller]  *
 *   [action]      *
 *   [coroutine]   !* [true/false]
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
return [
    'testCoroutine'  => ['module' => 'test', 'controller' => 'index', 'action' => 'index', 'coroutine' => true],
    'Index' => ['module' => 'index', 'controller' => 'index', 'action' => 'index'],
];