<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/21
 * Time: 10:47
 */

namespace Bc\App\Modules\Test\Models;


use Bc\App\Models\BaseModel;

class IndexModel extends BaseModel
{
  protected $dbName = 'blog';

  # Your Table name
  protected $table = 'blog_contents';

  # Primary Key of the Table
  protected $pk = 'cid';
}