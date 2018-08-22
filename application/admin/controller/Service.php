<?php
namespace app\admin\controller;

use think\Exception;

/**
 * 服务详情控制类
 *
 * Class Service
 * @package app\admin\controller
 */
class Service extends Base
{

    public $_sModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_sModel = new \app\common\model\Service();
    }

    /**
     * 列表
     * @return mixed
     */
    public function lists()
    {

        if (request()->isAjax()) {

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['title'] = ['like', '%' . $param['searchText'] . '%'];
            }

            $selectResult = $this->_sModel->getByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['create_time'] = date('Y-m-d H:i:s',$selectResult[$key]['create_time']);
                // $operate = ['编辑' => url('service/edit', ['id' => $vo['id']]), '删除' => "javascript:del('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'service/edit'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'service/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_sModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }

    //添加
    public function add()
    {
        //
        try {
            if (request()->isPost()) {
                $param = input();
                $param = parseParams($param['data']);
                $arr = ['title' => $param['title'], 'des' => $param['des'], 'tag' => $param['tag'], 'weight_val' => $param['weight_val']

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_sModel->insertService($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }

    //编辑
    public function edit()
    {
        try{
            if (request()->isPost()) {
                $param = input();
                $param = parseParams($param['data']);

                $arr = ['id' => $param['id'], 'title' => $param['title'], 'des' => $param['des'], 'tag' => $param['tag'], 'weight_val' => $param['weight_val']

                ];
                $arr['update_time'] = time();
                $flag = $this->_sModel->editService($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $aData = $this->_sModel->getById($id);

            $this->assign('data', $aData);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    //删除
    public function del()
    {
        $id = input('param.id');

        $flag = $this->_sModel->del($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}