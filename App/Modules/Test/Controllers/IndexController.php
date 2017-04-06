<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/10
 * Time: 14:59
 */

namespace Bc\App\Modules\Test\Controllers;

use Bc\App\Controllers\BaseController;
use Bc\App\Modules\Article\Models\IndexModel;
use Bc\Lib\Coroutine\SysCall;

class IndexController extends BaseController
{
  public function __construct()
  {
    $this->_modelMap = [
        'IndexModel' => IndexModel::class
    ];
  }

  public function indexAction()
  {
    $page     = isset($param['page']) ? intval($param['page']) : 1;
    $pageSize = isset($param['pageSize']) ? intval($param['pageSize']) : 5;

    yield $this->GenModel('IndexModel', function (IndexModel $model) use ($page, $pageSize, &$sCount, &$list)
    {
      $sCount = $model->getAllCount();

      $list = $model->getArticleList($page, $pageSize);
    });

    $data = [];
    foreach ($list as $key => $value)
    {
      $data[] = [
          'cid'      => $value['cid'],
          'title'    => $value['title'],
          'viewsNum' => $value['viewsNum'],
          'likesNum' => $value['likesNum']
      ];
    }

    $ret = [
        'sCount' => $sCount,
        'cCount' => count($data),
        'cList'  => $data,
    ];

    yield SysCall::end(['code' => 200, 'msg' => 'Success!', 'data' => $ret]);
  }
}