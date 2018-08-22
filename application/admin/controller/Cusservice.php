<?php

namespace app\admin\controller;


use app\common\controller\Sys;
use app\common\model\Cusservice as CsModel;
use app\common\model\Cusservgroup as CsGroup;
use think\Exception;

/**
 * 客服控制类
 *
 * Class User
 * @package app\admin\controller
 */
class Cusservice extends Base
{

    public $_csModel;
    public $_nId;
    public $_aData;

    public function _initialize()
    {
        parent::_initialize();
        $this->_csModel = new CsModel();

        if (in_array($this->request->action(), ['edit', 'del'])) {

            $this->_nId = input('param.id');
            if (!$this->_nId)
            {
                throw new Exception('数据错误');
            }
            $this->_aData = $this->_csModel->getById($this->_nId);

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
                $where['user_name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $selectResult = $this->_csModel->getByWhere($where, $offset, $limit);

            $status = config('cuss_status');

            //生成按钮
            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['user_avatar'] = '<img src="' . config('view_replace_str')['__UPLOADS__'] . '/' .$vo['user_avatar'] . '" width="40px" height="40px">';
                $selectResult[$key]['status'] = $status[$vo['status']];

                $operate = [
                    '编辑' => [
                        'href' => url('cusservice/edit', ['id' => $vo['id']]),
                        'auth' => 'cusservice/edit'
                    ],
                    '删除' => [
                        'href' => "javascript:del('".$vo['id']."')",
                        'auth' => 'cusservice/del'
                    ]
                ];

                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_csModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加用户
    public function add()
    {
        if(request()->isPost()){

            $param = input('param.');
            $param = parseParams($param['data']);

            // 检测头像
            if(empty($param['user_avatar'])){
                return json(['code' => 0, 'data' => '', 'msg' => '请上传头像']);
            }

            $param['user_pwd'] = md5($param['user_pwd']);
            if ($this->_csModel->where(['user_name' => $param['user_name']])->count())
            {
                return json(['code' => 0, 'data' => '', 'msg' => '该用户名已存在']);
            }
            $flag = $this->_csModel->insert($param);
            if ($flag['code'] > 0)
            {
                $this->logInfo('添加客服成功，名称：'. $param['user_name']);
            }

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $groupModel = new CsGroup();
        $group = $groupModel->where(['status' => Sys::COMMON_YES])->select();
        $this->assign([
            'groups' => $group,
            'status' => config('cuss_status')
        ]);

        return $this->fetch();
    }

    // 上传客服头像
    public function upAvatar()
    {
        if(request()->isAjax()) {

            $file = request()->file('file');
            if (!empty($file)) {
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    $photo_path = $info->getSaveName();

                    return json(['code' => 1, 'data' => ['src' => $photo_path], 'msg' => '上传成功']);
                } else {
                    // 上传失败获取错误信息
                    return json(['code' => 0, 'data' => '', 'msg' => $file->getError()]);
                }
            }
            else
            {
                return json(['code' => 0, 'data' => '', 'msg' => '数据为空']);
            }
        }
    }

    //编辑
    public function edit()
    {
        if(request()->isPost()){

            $param = input('post.');
            $param = parseParams($param['data']);

            // 修改用户头像
            if(empty($param['user_avatar'])){
                unset($param['user_avatar']);
            }

            if(empty($param['user_pwd'])){
                unset($param['user_pwd']);
            }else{
                $param['user_pwd'] = md5($param['user_pwd']);
            }
            $flag = $this->_csModel->edit($param);
            if ($flag['code'] > 0)
            {
                $this->logInfo('编辑客服成功，名称：'. $this->_aData['user_name']);
            }

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $groupModel = new CsGroup();
        $group = $groupModel->where(['status' => Sys::COMMON_YES])->select();
        $this->assign([
            'groups' => $group,
            'status' => config('cuss_status'),
            'data' => $this->_aData
        ]);
        return $this->fetch();
    }

    //删除
    public function del()
    {
        $flag = $this->_csModel->del($this->_nId);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除客服成功，名称：'. $this->_aData['user_name']);
        }
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}
