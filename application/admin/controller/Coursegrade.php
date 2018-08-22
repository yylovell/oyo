<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Sys;

class Coursegrade extends Base
{
    /**
     * @var object 模型
     */
    protected $_cgModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->_cgModel = new \app\common\model\Coursegrade();
    }

    // 节点列表
    public function index()
    {
        if(request()->isAjax()){

            $nodes = $this->_cgModel->getList();

            // 加状态显示
            foreach ($nodes as &$node)
            {
                if ($node['is_send'] == Sys::NEWS_IS_SEND)
                {
                    $node['name'] = $node['name'] . "  <span class='layui-badge-dot layui-bg-primary'></span>";
                }
                else
                {
                    $node['name'] = $node['name'] . "  <span class='layui-badge-dot layui-bg-gray'></span>";
                }
            }
            unset($node);

            $nodes = getTree(objToArray($nodes), false);
            return json(msg(1, $nodes, 'ok'));
        }

        return $this->fetch();
    }

    // 添加节点
    public function add()
    {
        $param = input('post.');
        $type_id = $param['type_id'];
        $is_send = $param['is_send'];

        if ($type_id)
        {
            $fatherData = $this->_cgModel->where(['id' => $type_id])->find();
            if ($is_send == Sys::NEWS_IS_SEND)
            {
                if ($fatherData['is_send'] != Sys::NEWS_IS_SEND)
                {
                    $this->_cgModel->where(['id' => $type_id])->update(['is_send' => Sys::NEWS_IS_SEND]);
                }
            }

        }

        $flag = $this->_cgModel->insert($param);
        if ($flag['code'] > 0)
        {
            $this->logInfo('添加等级成功，等级名称：' . $param['name']);
        }

        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 编辑节点
    public function edit()
    {
        $param = input('post.');
        $id = $param['id'];
        $is_send = $param['is_send'];
        $aData = $this->_cgModel->where(['id' => $id])->find();

        if ($is_send != $aData['is_send'])// 有变化
        {
            if ($aData['type_id'])// 不是父类
            {
                $fatherData = $this->_cgModel->where(['id' => $aData['type_id']])->find();

                if ($is_send == Sys::NEWS_IS_SEND)
                {
                    if ($fatherData['is_send'] != Sys::NEWS_IS_SEND)
                    {
                        $this->_cgModel->where(['id' => $aData['type_id']])->update(['is_send' => Sys::NEWS_IS_SEND]);
                    }
                }
                else
                {
                    $count = $this->_cgModel->where(['type_id' => $aData['type_id'], 'id' => ['neq', $id], 'is_send' => Sys::NEWS_IS_SEND])->count();
                    if (!$count)
                    {
                        $this->_cgModel->where(['id' => $aData['type_id']])->update(['is_send' => Sys::NEWS_NOT_SEND]);
                    }
                }
            }
            else
            {
                if ($is_send == Sys::NEWS_IS_SEND)
                {
                    $this->_cgModel->where(['type_id' => $id])->update(['is_send' => Sys::NEWS_IS_SEND]);
                }
                else
                {
                    $this->_cgModel->where(['type_id' => $id])->update(['is_send' => Sys::NEWS_NOT_SEND]);
                }
            }
        }
        else
        {//是父类，没变化，且启用状态，则开启所有子类
            if (!$aData['type_id'] && $is_send == Sys::NEWS_IS_SEND)
            {
                $this->_cgModel->where(['type_id' => $id])->update(['is_send' => Sys::NEWS_IS_SEND]);
            }
        }


        $flag = $this->_cgModel->edit($param);
        if ($flag['code'] > 0)
        {
            $this->logInfo('编辑等级成功，等级名称：' . $aData['name']);
        }


        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 删除节点
    public function del()
    {
        $id = input('param.id');
        $aData = $this->_cgModel->where(['id' => $id])->find();

        $flag = $this->_cgModel->del($id);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除等级成功，等级名称：' . $aData['name']);
        }
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }
}