<?php
/**
 * ---------------------------------------
 * #class_name
 * ---------------------------------------
 * [功能描述]
 * #class_info
 * ---------------------------------------
 *
 * @author Caiwh <caiwh@adnonstop.com>
 * @date   2017/3/6
 *         ---------------------------------------
 */

namespace Bc\Sys\AsyncIO;

/**
 * 异步Mysql,非同步阻塞业务使用,否则切记不要使用,将会导致数据无法输出
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class AsyncMysql
{
  public $db = null;

  static $dbs = null;

  public $result = null;

  public $config = null;

  public function __construct($config = null)
  {
    $this->config = $config;
  }

  public function connect($config, $sql)
  {
    $db = explode(',', $config['database']);

    $config = array_merge($config, ['database' => array_shift($db)]);

    $this->db = new \Swoole\MySQL();

    $resultInfo = &$this->result;

    $this->db->connect($config, function ($db, $result) use ($sql, &$resultInfo)
    {
      if ($result === false)
      {
        var_dump($db->connect_errno, $db->connect_error);
        die;
      }

      $this->db->query($sql, function ($db, $result) use (&$resultInfo)
      {
        if ($result === true)
        {
          var_dump($db->affected_rows, $db->insert_id);
        } else
        {

          var_dump($result);

          $resultInfo = $result;
        }
      });

      var_dump('down');
    });

    var_dump('connnn');

  }

  public function query($sql)
  {
    if (empty($sql))
    {
      return false;
    }

    var_dump('connect');

    $this->connect($this->config, $sql);

    var_dump('1');

    return $this->result;
  }

  public function close()
  {
    $this->db->close();
  }

}