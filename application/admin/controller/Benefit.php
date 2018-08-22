<?php
namespace app\admin\controller;

use think\Exception;

/**
 * 福利待遇控制类
 *
 * Class Benefit
 * @package app\admin\controller
 */
class Benefit extends Base
{

    public $_bModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_bModel = new \app\common\model\Benefit();
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

            $selectResult = $this->_bModel->getByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['create_time'] = date('Y-m-d H:i:s',$selectResult[$key]['create_time']);
                $selectResult[$key]['tag'] = '<i class="fa fa-'.$selectResult[$key]['tag'].'"></i>';
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'benefit/edit'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'benefit/del'
                    ]
                ];

                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_bModel->getAll($where);  //总数据
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

                $count = $this->_bModel->getAll([]);
                if ($count>=6){
                    throw new Exception('福利总数已达到6个，无法添加');
                }

                $param = input();
                $param = parseParams($param['data']);
                $arr = ['title' => $param['title'], 'des' => $param['des'], 'tag' => $param['tag']

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_bModel->insertBenefit($arr);

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

                $arr = ['id' => $param['id'], 'title' => $param['title'], 'des' => $param['des'], 'tag' => $param['tag']

                ];
                $arr['update_time'] = time();
                $flag = $this->_bModel->editBenefit($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $aData = $this->_bModel->getById($id);

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

        $flag = $this->_bModel->del($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}