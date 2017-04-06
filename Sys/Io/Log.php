<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/20
 * Time: 15:01
 */

namespace Bc\Sys\Io;

/**
 * IO日志类
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class Log
{
  # @string, Log directory name
  private $path = '/logs/mysql/';

  # @void, Default Constructor, Sets the timezone and path of the log files.
  public function __construct()
  {
    $this->path = dirname(__DIR__,2) . $this->path;
  }

  /**
   * @void
   *  Creates the log
   *
   * @param string $message the message which is written into the log.
   *
   * @description:
   *   1. Checks if directory exists, if not, create one and call this method again.
   *   2. Checks if log already exists.
   *   3. If not, new log gets created. Log is written into the logs folder.
   *   4. Logname is current date(Year - Month - Day).
   *   5. If log exists, edit method called.
   *   6. Edit method modifies the current log.
   */
  public function write($message)
  {
    $date = new \DateTime();
    $log  = $this->path . $date->format('Y-m-d') . ".txt";

    if (is_dir($this->path))
    {
      if (!file_exists($log))
      {
        $fh = fopen($log, 'a+') or die("Fatal Error !");
        $logcontent = "Time : " . $date->format('H:i:s') . PHP_EOL . $message . PHP_EOL;
        fwrite($fh, $logcontent);
        fclose($fh);
      } else
      {
        $this->edit($log, $date, $message);
      }
    } else
    {
      if (mkdir($this->path, 0777) === true)
      {
        $this->write($message);
      }
    }
  }

  /**
   * @void
   *  Gets called if log exists.
   *  Modifies current log and adds the message to the log.
   *
   * @param string    $log
   * @param \DateTime $date
   * @param string    $message
   */
  private function edit($log, $date, $message)
  {
    $logcontent = "Time : " . $date->format('H:i:s') . PHP_EOL . $message . PHP_EOL;
    $logcontent = $logcontent . file_get_contents($log);
    file_put_contents($log, $logcontent);
  }

}