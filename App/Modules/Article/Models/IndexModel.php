<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/21
 * Time: 10:47
 */

namespace Bc\App\Modules\Article\Models;


use Bc\App\Models\BaseModel;
use Bc\Sys\Io\MysqlPdo;
use Bc\Sys\Ioc\MySqlPool;

class IndexModel extends BaseModel
{
  protected $dbName = 'blog';

  # Your Table name
  protected $table = 'blog_contents';

  # Primary Key of the Table
  protected $pk = 'cid';

  /**
   * 获取文章分页列表
   *
   * @param int    $page
   * @param int    $pageSize
   * @param string $sort_field
   * @param string $sort
   *
   * @return mixed
   */
  public function getArticleList($page = 1, $pageSize = 5, $sort_field = 'cid', $sort = 'DESC')
  {
    $this->Go(function (MysqlPdo $mysql) use (&$list, $page, $pageSize, $sort, $sort_field)
    {
      $field = '*';

      $offset = ($page - 1) * $pageSize;

      $order_by = "ORDER BY {$sort_field} {$sort}";

      $where = 'WHERE parent = 0 AND template is NULL';

      $sql = "select {$field} from {$this->dbName}.{$this->table} {$where} {$order_by} LIMIT {$offset},{$pageSize} ";

      $list = $mysql->query($sql);
    });

    return $list;
  }

  /**
   * 获取总数
   *
   * @return mixed
   */
  public function getAllCount()
  {
    $this->Go(function (MysqlPdo $mysql) use (&$data)
    {
      $where = 'WHERE parent = 0 AND template is NULL';

      $sql = "select count(*) as c from {$this->dbName}.{$this->table} {$where}";

      $data = $mysql->query($sql);
      $data = $data[0]['c'];

    });

    return $data;
  }

  public function getArticleInfo($cid)
  {
    $this->Go(function (MysqlPdo $mysql) use ($cid, &$data)
    {

      $field = '*';

      $where = 'WHERE cid = ' . $cid;

      $sql = "select {$field} from {$this->dbName}.{$this->table} {$where} ";

      $data = $mysql->query($sql);

      if (!empty($data))
      {
        $data = array_shift($data);
      }
    });

    return $data;
  }
}