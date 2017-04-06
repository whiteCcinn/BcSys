<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/21
 * Time: 10:06
 */

namespace Bc\App\Models;


use Bc\Sys\Ioc\MySqlPool;

/**
 * 基本控制model
 *
 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class BaseModel
{
  /**
   * 切换数据库
   *
   * @var
   */
  protected $dbName = null;

  /**
   * CURD关联数据表
   *
   * @var
   */
  protected $table;

  /**
   * 主键字段
   *
   * @var
   */
  protected $pk;

  /**
   * db句柄
   *
   * @var mixed
   */
  private $db;

  /**
   * 数据字段
   *
   * @var array
   */
  public $variables;

  public function __construct($data = array())
  {
    $this->db        = MySqlPool::pop($this->dbName);
    $this->variables = $data;
  }

  public function __set($name, $value)
  {
    if (strtolower($name) === $this->pk)
    {
      $this->variables[ $this->pk ] = $value;
    } else
    {
      $this->variables[ $name ] = $value;
    }
  }

  public function __get($name)
  {

    if (is_array($this->variables))
    {
      if (array_key_exists($name, $this->variables))
      {
        return $this->variables[ $name ];
      }
    }

    return null;
  }


  public function save($id = "0")
  {
    $this->variables[ $this->pk ] = (empty($this->variables[ $this->pk ])) ? $id : $this->variables[ $this->pk ];

    $fieldsvals = '';
    $columns    = array_keys($this->variables);

    foreach ($columns as $column)
    {
      if ($column !== $this->pk)
        $fieldsvals .= $column . " = :" . $column . ",";
    }

    $fieldsvals = substr_replace($fieldsvals, '', -1);

    if (count($columns) > 1)
    {

      $sql = "UPDATE " . $this->table . " SET " . $fieldsvals . " WHERE " . $this->pk . "= :" . $this->pk;
      if ($id === "0" && $this->variables[ $this->pk ] === "0")
      {
        unset($this->variables[ $this->pk ]);
        $sql = "UPDATE " . $this->table . " SET " . $fieldsvals;
      }

      return $this->exec($sql);
    }

    return null;
  }

  public function create()
  {
    $bindings = $this->variables;

    if (!empty($bindings))
    {
      $fields     = array_keys($bindings);
      $fieldsvals = array(implode(",", $fields), ":" . implode(",:", $fields));
      $sql        = "INSERT INTO " . $this->table . " (" . $fieldsvals[0] . ") VALUES (" . $fieldsvals[1] . ")";
    } else
    {
      $sql = "INSERT INTO " . $this->table . " () VALUES ()";
    }

    return $this->exec($sql);
  }

  public function delete($id = "")
  {
    $id = (empty($this->variables[ $this->pk ])) ? $id : $this->variables[ $this->pk ];

    if (!empty($id))
    {
      $sql = "DELETE FROM " . $this->table . " WHERE " . $this->pk . "= :" . $this->pk . " LIMIT 1";
    }

    return $this->exec($sql, array($this->pk => $id));
  }

  public function find($id = "")
  {
    $id = (empty($this->variables[ $this->pk ])) ? $id : $this->variables[ $this->pk ];

    if (!empty($id))
    {
      $sql = "SELECT * FROM " . $this->table . " WHERE " . $this->pk . "= :" . $this->pk . " LIMIT 1";

      $result          = $this->db->row($sql, array($this->pk => $id));
      $this->variables = ($result != false) ? $result : null;

      $this->free();

      return $result;
    }

    $this->free();

    return false;
  }

  /**
   * @param array $fields .
   * @param array $sort .
   *
   * @return array of Collection.
   * Example: $user = new User;
   * $found_user_array = $user->search(array('sex' => 'Male', 'age' => '18'), array('dob' => 'DESC'));
   * // Will produce: SELECT * FROM {$this->table_name} WHERE sex = :sex AND age = :age ORDER BY dob DESC;
   * // And rest is binding those params with the Query. Which will return an array.
   * // Now we can use for each on $found_user_array.
   * Other functionalities ex: Support for LIKE, >, <, >=, <= ... Are not yet supported.
   */
  public function search($fields = array(), $sort = array())
  {
    $bindings = empty($fields) ? $this->variables : $fields;

    $sql = "SELECT * FROM " . $this->table;

    if (!empty($bindings))
    {
      $fieldsvals = array();
      $columns    = array_keys($bindings);
      foreach ($columns as $column)
      {
        $fieldsvals [] = $column . " = :" . $column;
      }
      $sql .= " WHERE " . implode(" AND ", $fieldsvals);
    }

    if (!empty($sort))
    {
      $sortvals = array();
      foreach ($sort as $key => $value)
      {
        $sortvals[] = $key . " " . $value;
      }
      $sql .= " ORDER BY " . implode(", ", $sortvals);
    }

    return $this->exec($sql);
  }

  public function all()
  {
    $result = $this->db->query("SELECT * FROM " . $this->table);

    return $result;
  }

  public function min($field)
  {
    if ($field)
    {
      $result = $this->db->single("SELECT min(" . $field . ")" . " FROM " . $this->table);

      $this->free();

      return false;
    }
  }

  public function max($field)
  {
    if ($field)
    {
      $result = $this->db->single("SELECT max(" . $field . ")" . " FROM " . $this->table);

      $this->free();

      return $result;
    }

    return false;
  }

  public function avg($field)
  {
    if ($field)
    {
      $result = $this->db->single("SELECT avg(" . $field . ")" . " FROM " . $this->table);

      $this->free();

      return $result;
    }

    return false;
  }

  public function sum($field)
  {
    if ($field)
    {
      $result = $this->db->single("SELECT sum(" . $field . ")" . " FROM " . $this->table);

      $this->free();

      return $result;
    }

    return false;
  }

  public function count($field)
  {
    if ($field)
    {
      $result = $this->db->single("SELECT count(" . $field . ")" . " FROM " . $this->table);

      $this->free();

      return $result;
    }

    return false;
  }


  private function exec($sql, $array = null)
  {

    if ($array !== null)
    {
      // Get result with the DB object
      $result = $this->db->query($sql, $array);
    } else
    {
      // Get result with the DB object
      $result = $this->db->query($sql, $this->variables);
    }

    $this->free();

    return $result;
  }

  private function free()
  {
    MySqlPool::push($this->db, $this->dbName);
    // Empty bindings
//    $this->variables = array();
    $this->db = null;
  }

  public function close()
  {
    if (is_object($this->db) && !is_null($this->db))
      MySqlPool::push($this->db);
  }

  /**
   * Go Model
   *
   * @param callable $function 回调函数
   */
  public function Go($function)
  {
    $mysql    = MySqlPool::pop($this->dbName);

    $data = $function($mysql);

    MySqlPool::push($mysql, $this->dbName);

    return $data;
  }
}