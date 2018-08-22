<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 案例分类控制类
 *
 * Class Casecat
 * @package app\admin\controller
 */
class Casecat extends Base
{

    public $_ccModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_ccModel = new \app\common\model\Casecat();
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
                $where['name'] = ['like', '%' . $param['searchText'] . '%'];
            }

            $selectResult = $this->_ccModel->getByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['create_time'] = date('Y-m-d H:i:s', $selectResult[$key]['create_time']);
                $selectResult[$key]['is_send'] = Sys::getIsSend($selectResult[$key]['is_send']);
                //$operate = ['编辑' => url('edit', ['id' => $vo['id']])];
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'casecat/edit'
                    ]
                ];

                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_ccModel->getAll($where);  //总数据
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
                $param['is_send'] = isset($param['is_send']) ?: 0;
                $arr = ['name' => $param['name'], 'des' => $param['des'], 'is_send' => $param['is_send'], 'weight_val' => $param['weight_val']

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_ccModel->insertCaseCat($arr);

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
        try {
            if (request()->isPost()) {
                $param = input();
                $param = parseParams($param['data']);
                $param['is_send'] = isset($param['is_send']) ?: 0;
                $arr = ['id' => $param['id'], 'name' => $param['name'], 'des' => $param['des'], 'is_send' => $param['is_send'], 'weight_val' => $param['weight_val']

                ];
                $arr['update_time'] = time();
                $flag = $this->_ccModel->editCaseCat($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $aData = $this->_ccModel->getById($id);

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

        $flag = $this->_ccModel->del($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}