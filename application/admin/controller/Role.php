<?php

namespace app\admin\controller;

use app\admin\model\Node;
use app\admin\model\UserType;

/**
 * 角色控制类
 *
 * Class Role
 * @package app\admin\controller
 */
class Role extends Base
{
    //角色列表
    public function index()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['rolename'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $user = new UserType();
            $selectResult = $user->getRoleByWhere($where, $offset, $limit);

            foreach($selectResult as $key=>$vo){

                if(1 == $vo['id']){
                    $selectResult[$key]['operate'] = '';
                    continue;
                }

                /*$operate = [
                    '编辑' => url('role/roleEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:roleDel('".$vo['id']."')",
                    '分配权限' => "javascript:giveAccess('".$vo['id']."')"
                ];*/

                $operate = [
                    '编辑' => [
                        'href' => url('role/roleEdit', ['id' => $vo['id']]),
                        'auth' => 'role/roleedit'
                    ],
                    '删除' => [
                        'href' => "javascript:roleDel('".$vo['id']."')",
                        'auth' => 'role/roledel'
                    ],
                    '分配权限' => [
                        'href' => "javascript:giveAccess('".$vo['id']."')",
                        'auth' => 'role/giveaccess'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $user->getAllRole($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加角色
    public function roleAdd()
    {
        if(request()->isPost()){

            $param = input('param.');
            $param = parseParams($param['data']);

            $role = new UserType();
            $flag = $role->insertRole($param);
            if ($flag['code'] > 0)
            {
                $this->logInfo('添加角色成功，角色名称：'. $param['rolename']);
            }

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        return $this->fetch();
    }

    //编辑角色
    public function roleEdit()
    {
        $role = new UserType();

        if(request()->isPost()){

            $param = input('post.');
            $param = parseParams($param['data']);

            $flag = $role->editRole($param);
            if ($flag['code'] > 0)
            {
                $this->logInfo('编辑角色成功，角色名称：'. $param['rolename']);
            }

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign([
            'role' => $role->getOneRole($id)
        ]);
        return $this->fetch();
    }

    //删除角色
    public function roleDel()
    {
        $id = input('param.id');

        $role = new UserType();
        $aData = $role->getOneRole($id);

        $flag = $role->delRole($id);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除角色成功，角色名称：'. $aData['rolename']);
        }
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    //分配权限
    public function giveAccess()
    {
        $param = input('param.');
        $node = new Node();
        //获取现在的权限
        if('get' == $param['type']){

            $nodeStr = $node->getNodeInfo($param['id']);
            return json(['code' => 1, 'data' => $nodeStr, 'msg' => 'success']);
        }
        //分配新权限
        if('give' == $param['type']){

            $doparam = [
                'id' => $param['id'],
                'rule' => $param['rule']
            ];
            $user = new UserType();
            $aData = $user->getOneRole($param['id']);

            $flag = $user->editAccess($doparam);
            if ($flag['code'] > 0)
            {
                $this->logInfo('分配角色权限，角色名称：'. $aData['rolename']);
            }
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
    }
}