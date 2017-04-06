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
        $page = isset($param['page']) ? intval($param['page']) : 1;
        $pageSize = isset($param['pageSize']) ? intval($param['pageSize']) : 5;

        $this->GenModel('IndexModel', function (IndexModel $model) use ($page, $pageSize, &$sCount, &$list) {
            $sCount = $model->getAllCount();

            $list = $model->getArticleList($page, $pageSize);
        });

        $data = [];
        foreach ($list as $key => $value) {
            $data[] = [
                'cid' => $value['cid'],
                'title' => !empty($value['password']) ? '该文章已加密' : $value['title'],
                'viewsNum' => $value['viewsNum'],
                'likesNum' => $value['likesNum'],
                'passwd' => !empty($value['password']) ? true : false
            ];
        }

        $ret = [
            'sCount' => $sCount,
            'cCount' => count($data),
            'cList' => $data,
        ];

        Response::Out(['code' => 200, 'msg' => 'Success!', 'data' => $ret]);
    }

    /**
     * 获取作品信息
     * @param array $param
     *
     * @return bool
     */
    public function getArticleInfoAction($param = [])
    {

        $this->GenModel('IndexModel', function (IndexModel $model) use (&$info, $param) {
            $info = $model->getArticleInfo($param['cid']);
        });

        if (empty($info)) {
            return Response::Out(['code' => -100, 'msg' => 'Data does not exists!']);
        }

        $ret = [
            'content' => $info['text']
        ];

        Response::Out(['code' => 200, 'msg' => 'Success!', 'data' => $ret]);
    }
}