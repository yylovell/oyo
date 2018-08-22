<?php
namespace app\admin\controller;

/**
 * 菜单控制类
 *
 * Class Node
 * @package app\admin\controller
 */
class Node extends Base
{

    public $_nModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_nModel = new \app\admin\model\Node();
    }

    // 节点列表
    public function index()
    {
        if(request()->isAjax()){

            $nodes = $this->_nModel->getList();

            $nodes = getTree(objToArray($nodes), false);
            return json(msg(1, $nodes, 'ok'));
        }

        return $this->fetch();
    }

    // 添加节点
    public function add()
    {
        $param = input('post.');

        $flag = $this->_nModel->insert($param);

        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 编辑节点
    public function edit()
    {
        $param = input('post.');

        $flag = $this->_nModel->edit($param);

        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 删除节点
    public function del()
    {
        $id = input('param.id');

        $flag = $this->_nModel->del($id);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }
}