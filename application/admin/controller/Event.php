<?php
namespace app\admin\controller;

use think\Exception;

/**
 * 大事记控制类
 *
 * Class Event
 * @package app\admin\controller
 */
class Event extends Base
{

    public $_Model;

    public function _initialize()
    {
        parent::_initialize();
        $this->_Model = new \app\common\model\Event();
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
            /*if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['name'] = ['like', '%' . $param['searchText'] . '%'];
            }*/


            $selectResult = $this->_Model->getByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {

                // $operate = ['编辑' => url('event/edit', ['id' => $vo['id']]), '删除' => "javascript:del('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'event/edit'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'event/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_Model->getAll($where);  //总数据
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
                $param = $_POST;

                $arr = ['time' => $param['time'], 'des' => $param['des'],

                ];
                $arr['create_time'] = $arr['update_time'] = time();
                $flag = $this->_Model->insertEvent($arr);

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
                $param = $_POST;

                $arr = ['id' => $param['id'], 'time' => $param['time'], 'des' => $param['des'],

                ];
                $arr['update_time'] = time();
                $flag = $this->_Model->editEvent($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $Data = $this->_Model->getById($id);
            $this->assign('data', $Data);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    //删除职位
    public function del()
    {
        $id = input('param.id');


        $flag = $this->_Model->del($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}