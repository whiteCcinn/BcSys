<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/10
 * Time: 14:59
 */

namespace Bc\App\Modules\Index\Controllers;

use Bc\App\Controllers\BaseController;
use Bc\App\Modules\Index\Models\IndexModel;
use Bc\Sys\Response\Response;


class IndexController extends BaseController
{
    public function __construct()
    {
        $this->_modelMap = [
            'IndexModel' => IndexModel::class
        ];
    }

    public function IndexAction($param = [])
    {
//        Response::Out(['code' => 200, 'msg' => 'Success!', 'data' => 1]);
      $this->display();
    }
}