<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/10
 * Time: 14:59
 */

namespace Bc\App\Modules\Article\Controllers;

use Bc\App\Controllers\BaseController;
use Bc\App\Modules\Article\Models\IndexModel;
use Bc\Sys\Response\Response;


class IndexController extends BaseController
{
    public function __construct()
    {
        $this->_modelMap = [
            'IndexModel' => IndexModel::class
        ];
    }

    /**
     * 作品列表
     * @param array $param
     */
    public function getArticleListAction($param = [])
    {
        Response::Out(['code' => 200, 'msg' => 'Success!', 'data' => 1]);
    }
}