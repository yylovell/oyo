<?php

namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;
/**
 * 客服分组控制类
 *
 * Class Role
 * @package app\admin\controller
 */
class Cusservgroup extends Base
{
    public $_gModel;
    public $_nId;
    public $_aData;

    public function _initialize()
    {
        parent::_initialize();

        $this->_gModel = new \app\common\model\Cusservgroup();

        if (in_array($this->request->action(), ['edit', 'del'])) {

            $this->_nId = input('param.id');
            if (!$this->_nId)
            {
                throw new Exception('数据错误');
            }
            $this->_aData = $this->_gModel->getById($this->_nId);

        }
    }

    //列表
    public function lists()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $selectResult = $this->_gModel->getByWhere($where, $offset, $limit);

            $cusservModel = new \app\common\model\Cusservice();

            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['status'] = Sys::getCommonIS($selectResult[$key]['status']);

                // 统计分组人数
                $selectResult[$key]['num'] = $cusservModel->where('group_id', $vo['id'])->count();

                $operate = [
                    '编辑' => [
                        'href' => url('cusservgroup/edit', ['id' => $vo['id']]),
                        'auth' => 'cusservgroup/edit'
                    ],
                    '删除' => [
                        'href' => "javascript:del('".$vo['id']."')",
                        'auth' => 'cusservgroup/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_gModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加
    public function add()
    {
        try
        {
            if(request()->isPost()){

                $param = $_POST;

                $count = $this->_gModel->where('name', $param['name'])->count();
                if($count){
                    throw new Exception('该分组已经存在');
                }

                $flag = $this->_gModel->insert($param);

                if ($flag['code'] > 0)
                {
                    $this->logInfo('添加客服分组成功，名称：'. $param['name']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            return $this->fetch();
        }
        catch (Exception $e)
        {
            return json(['code' => 0, 'data' => '', 'msg' => $e->getMessage()]);
        }

    }

    //编辑
    public function edit()
    {
        try
        {
            if(request()->isPost()){

                $param = $_POST;

                $count = $this->_gModel->where(['name' => $param['name'], 'id' => ['<>', $param['id']]])->count();
                if($count){
                    throw new Exception('该分组已经存在');
                }

                $flag = $this->_gModel->edit($param);

                if ($flag['code'] > 0)
                {
                    $this->logInfo('编辑客服分组成功，id：'. $param['id']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
            $this->assign(['data' => $this->_aData, 'status' => Sys::getCommonIS()]);

            return $this->fetch();
        }
        catch (Exception $e)
        {
            return json(['code' => 0, 'data' => '', 'msg' => $e->getMessage()]);
        }
    }

    //删除
    public function del()
    {
        return json(['code' => 0, 'data' => '', 'msg' => '客服分组暂不支持删除，禁用即可！']);
        // 查询该分组下是否有客服
        $cusservModel = new \app\common\model\Cusservice();
        $has = $cusservModel->where('group_id', $this->_nId)->count();
        if($has > 0){
            return json(['code' => 0, 'data' => '', 'msg' => '该分组下有客服，不可删除']);
        }

        $flag = $this->_gModel->del($this->_nId);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除分组成功，名称：'. $this->_aData['name']);
        }
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    // 管理组员
    public function manageUser()
    {
        return $this->fetch();
    }
}