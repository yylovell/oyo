<?php

namespace app\admin\controller;


use app\admin\model\UserModel;
use app\admin\model\UserType;
use think\Exception;

/**
 * 用户控制类
 *
 * Class User
 * @package app\admin\controller
 */
class User extends Base
{

    public $_uModel;
    public $_nId;
    public $_aData;

    public function _initialize()
    {
        parent::_initialize();
        $this->_uModel = new UserModel();

        if (in_array($this->request->action(), ['useredit', 'userdel'])) {

            $this->_nId = input('param.id');
            if (!$this->_nId)
            {
                throw new Exception('数据错误');
            }
            $this->_aData = $this->_uModel->getOneUser($this->_nId);

        }
    }

    //用户列表
    public function index()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['username'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $selectResult = $this->_uModel->getUsersByWhere($where, $offset, $limit);

            $status = config('user_status');

            //生成按钮
            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['user_avatar'] = '<img src="' . config('view_replace_str')['__UPLOADS__'] . '/' .$vo['user_avatar'] . '" width="40px" height="40px">';
                $selectResult[$key]['last_login_time'] = date('Y-m-d H:i:s', $vo['last_login_time']);
                $selectResult[$key]['status'] = $status[$vo['status']];

                $operate = [
                    '编辑' => [
                        'href' => url('user/userEdit', ['id' => $vo['id']]),
                        'auth' => 'user/useredit'
                    ],
                    '删除' => [
                        'href' => "javascript:userDel('".$vo['id']."')",
                        'auth' => 'user/userdel'
                    ]
                ];

                if( 1 == $vo['id'] ){
                    $operate = [
                        '编辑' => [
                            'href' => url('user/userEdit', ['id' => $vo['id']]),
                            'auth' => 'user/useredit'
                        ]
                    ];
                }

                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_uModel->getAllUsers($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加用户
    public function userAdd()
    {
        if(request()->isPost()){

            $param = input('param.');
            $param = parseParams($param['data']);

            // 检测头像
            if(empty($param['user_avatar'])){
                return json(['code' => 0, 'data' => '', 'msg' => '请上传头像']);
            }

            $param['password'] = md5($param['password']);
            if ($this->_uModel->where(['username' => $param['username']])->count())
            {
                return json(['code' => 0, 'data' => '', 'msg' => '该用户名已存在']);
            }
            $flag = $this->_uModel->insertUser($param);
            if ($flag['code'] > 0)
            {
                $this->logInfo('添加用户成功，用户名称：'. $param['username']);
            }

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $role = new UserType();
        $this->assign([
            'role' => $role->getRole(),
            'status' => config('user_status')
        ]);

        return $this->fetch();
    }

    //编辑
    public function userEdit()
    {

        if(request()->isPost()){

            $param = input('post.');
            $param = parseParams($param['data']);

            // 修改用户头像
            if(empty($param['user_avatar'])){
                unset($param['user_avatar']);
            }

            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5($param['password']);
            }
            $flag = $this->_uModel->editUser($param);
            if ($flag['code'] > 0)
            {
                $this->logInfo('编辑用户成功，用户名称：'. $this->_aData['username']);
            }

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $role = new UserType();
        $this->assign([
            'user' => $this->_aData,
            'status' => config('user_status'),
            'role' => $role->getRole()
        ]);
        return $this->fetch();
    }

    //删除
    public function userDel()
    {
        //return json(['code' => 0, 'data' => '', 'msg' => '删除失败啦哈哈哈']);
        $flag = $this->_uModel->delUser($this->_nId);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除用户成功，用户名称：'. $this->_aData['username']);
        }
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}
