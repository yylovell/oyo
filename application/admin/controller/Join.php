<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 职位控制类
 *
 * Class Join
 * @package app\admin\controller
 */
class Join extends Base
{

    public $_jModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_jModel = new \app\admin\model\Join();
    }

    /**
     * 职位列表
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


            $selectResult = $this->_jModel->getJoinByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {

                $selectResult[$key]['is_send'] = Sys::getIsSend($selectResult[$key]['is_send']);
                $selectResult[$key]['create_time'] = date('Y-m-d H:i:s',$selectResult[$key]['create_time']);

                // $operate = ['编辑' => url('join/joinEdit', ['id' => $vo['id']]), '删除' => "javascript:joinDel('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('join/joinEdit', ['id' => $vo['id']]),
                        'auth' => 'join/joinedit'
                    ],
                    '删除' => [
                        'href' => "javascript:joinDel('" . $vo['id'] . "')",
                        'auth' => 'join/joindel'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_jModel->getAllJoin($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加职位
    public function joinAdd()
    {
        //
        try {
            if (request()->isPost()) {
                $param = $_POST;

                $arr = ['name' => $param['name'], 'address' => $param['address'], 'is_send' => $param['is_send'],  'des' => $param['des'],

                ];
                $arr['create_time'] = $arr['update_time'] = time();
                $flag = $this->_jModel->insertJoin($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }

    //编辑职位
    public function joinEdit()
    {
        try{
            if (request()->isPost()) {
                $param = $_POST;

                $arr = ['id' => $param['id'], 'name' => $param['name'], 'address' => $param['address'], 'is_send' => $param['is_send'], 'des' => $param['des'],

                ];
                $arr['update_time'] = time();
                $flag = $this->_jModel->editJoin($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $joinData = $this->_jModel->getJoinById($id);
            $this->assign(['data' => $joinData]);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    //删除职位
    public function joinDel()
    {
        $id = input('param.id');


        $flag = $this->_jModel->delJoin($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}