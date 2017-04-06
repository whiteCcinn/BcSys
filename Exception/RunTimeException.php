<?php
namespace Bc\Exception;

use \Exception;

/**
 * 运行时的异常类
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class RunTimeException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous)
    {
        parent::__construct($message, $code, $previous);
    }
}