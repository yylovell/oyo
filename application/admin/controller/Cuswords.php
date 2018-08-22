<?php

namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;
/**
 * 常用语控制类
 *
 * Class Cuswords
 * @package app\admin\controller
 */
class Cuswords extends Base
{
    public $_cwModel;
    public $_nId;
    public $_aData;

    public function _initialize()
    {
        parent::_initialize();

        $this->_cwModel = new \app\common\model\Cuswords();

        if (in_array($this->request->action(), ['edit', 'del'])) {

            $this->_nId = input('param.id');
            if (!$this->_nId)
            {
                throw new Exception('数据错误');
            }
            $this->_aData = $this->_cwModel->getById($this->_nId);

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
                $where['content'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $selectResult = $this->_cwModel->getByWhere($where, $offset, $limit);

            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['status'] = Sys::getCommonIS($vo['status']);

                $operate = [
                    '编辑' => [
                        'href' => url('cuswords/edit', ['id' => $vo['id']]),
                        'auth' => 'cuswords/edit'
                    ],
                    '删除' => [
                        'href' => "javascript:del('".$vo['id']."')",
                        'auth' => 'cuswords/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_cwModel->getAll($where);  //总数据
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

                $param = input('post.');
                $param['content'] = trim($param['content']);

                $flag = $this->_cwModel->insert($param);

                if ($flag['code'] > 0)
                {
                    $this->logInfo('添加常用语成功，ID：'. $flag['data']);
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

                $param = input('post.');
                $param['content'] = trim($param['content']);

                $flag = $this->_cwModel->edit($param);

                if ($flag['code'] > 0)
                {
                    $this->logInfo('编辑常用语成功，id：'. $param['id']);
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
        $flag = $this->_cwModel->del($this->_nId);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除常用语成功，ID：'. $this->_nId);
        }
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}