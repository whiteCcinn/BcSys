<?php
namespace Bc\Lib\Coroutine;

use Bc\Sys\Response\Response;

/**
 * Class Task
 *
 * @package Bc\Lib\Coroutine
 */
class Task
{

  protected $callbackData;
  protected $taskId;
  protected $corStack;
  protected $coroutine;
  protected $exception = null;

  /**
   * [__construct 构造函数，生成器+taskId, taskId由 scheduler管理]
   *
   * @param Generator $coroutine [description]
   * @param [type]    $task      [description]
   */
  public function __construct($taskId, \Generator $coroutine)
  {
    $this->taskId    = $taskId;
    $this->coroutine = $coroutine;
    $this->corStack  = new \SplStack();
  }

  /**
   * [getTaskId 获取task id]
   *
   * @return [type] [description]
   */
  public function getTaskId()
  {
    return $this->taskId;
  }

  /**
   * [run 协程调度]
   *
   * @param  Generator $gen [description]
   *
   * @return [type]         [description]
   */
  public function run(\Generator $gen)
  {

    while (true)
    {

      try
      {

        $value = $gen->current();

        $this->corStack->push($gen);

        /*
            中断内嵌 继续入栈
         */
        if ($value instanceof \Generator)
        {
          $gen = $value;
          continue;
        }

        if ($value instanceof \Bc\Lib\Coroutine\RetVal)
        {
          // end yeild

          Response::Out($value->info);
          return false;
        }

        $gen = $this->corStack->pop();
        $gen->send($value);

      } catch (\Exception $e)
      {

        if ($this->corStack->isEmpty())
        {

          /*
              throw the exception
          */
          return $e->getMessage();
        }
      }
    }
  }

  /**
   * [isFinished 判断该task是否完成]
   *
   * @return boolean [description]
   */
  public function isFinished()
  {
    return !$this->coroutine->valid();
  }

  public function getCoroutine()
  {

    return $this->coroutine;
  }
}
