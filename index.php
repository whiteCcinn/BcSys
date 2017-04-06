<?php

/**
 * 框架入口文件
 *
 * 框架要进入，就必须经过这么一个单一入口文件
 *
 * @author      caiwh<471113744@qq.com>
 * @version     1.0.0
 * @since       1.0.0
 */

namespace BcIndexConstant {
    const BC_VERSION       = '1.0.0';
    const EXC_FILE         = '.php';
    const PREFIX_NAMESPACE = 'Bc';
    const ROOT_PATH        = __DIR__ . DIRECTORY_SEPARATOR;
    const APP_PATH         = ROOT_PATH . 'App' . DIRECTORY_SEPARATOR;
    const COMMON_PATH      = ROOT_PATH . 'Common' . DIRECTORY_SEPARATOR;
    const CONFIG_PATH      = ROOT_PATH . 'Config' . DIRECTORY_SEPARATOR;
    const LIB_PATH         = ROOT_PATH . 'Lib' . DIRECTORY_SEPARATOR;
    const SYS_PATH         = ROOT_PATH . 'Sys' . DIRECTORY_SEPARATOR;
}

/**
 * 入口类
 *
 * 入口类，需要启动框架入口
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
namespace Bc {

    use Bc\App\Bootstrap;

    class Index
    {
        public static $instance = null;

        public static $bootstrap = null;

        /**
         * Index constructor.
         */
        private function __construct()
        {
            $this->_loadBootstrap();
        }

        /**
         * Run
         *
         * @return null
         */
        public static function run()
        {
            if (static::$instance == null)
            {
                static::$instance = new self;
            }

            return static::$instance;
        }

        /**
         * Load the Bootstrap the file of php
         */
        private function _loadBootstrap()
        {
            require_once \BcIndexConstant\APP_PATH . 'Bootstrap' . \BcIndexConstant\EXC_FILE;

            Bootstrap::bootstrap();
        }
    }
}