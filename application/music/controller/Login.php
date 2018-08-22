<?php

namespace app\music\controller;

use app\common\controller\Sys;
use think\console\Input;
use think\Controller;
use think\Exception;

/**
 * 登录控制类
 *
 * Class Login
 * @package app\admin\controller
 */
class Login extends Controller
{

    public $_sModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_sModel = new \app\common\model\Students();
    }

    //登录页面
    public function index()
    {
        try
        {
            if (request()->isPost())
            {
                $param = input();
                if (!trim($param['password']))
                {
                    throw new Exception('登录密码非法');
                }
                $student = $this->_sModel->getByPhone($param['phone']);
                if (empty($student))
                {
                    throw new Exception('登录账户不存在');
                }

                if ($student['is_send'] != Sys::NEWS_IS_SEND)
                {
                    throw new Exception('禁止登录');
                }
                if (md5($param['password']) == $student['password'])
                {

                    if ($param['auto'] = 1)
                    {
                        cookie('student_phone', $student['phone'], 31536000);
                    }
                    else
                    {
                        cookie('student_phone', $student['phone'], 3600);
                    }

                    return ['code' => 1, 'data' => '', 'msg' => '登录成功'];
                }
                else
                {
                    throw new Exception('登录密码错误');
                }
            }
            /*if (!session('wxmember'))
            {
                \app\music\controller\Wx::getUserDetail();
            }

            $wxmember = session('wxmember');

            $open_id = $wxmember['openid'];
            $wxmemberObj = new \app\common\model\Wxmembers();
            $wxmemberData = $wxmemberObj->getByOpenId($open_id);

            if (!$wxmemberData)
            {
                $aInput = [
                    'sex' => $wxmember['sex'],
                    'mp_id' => $open_id,
                    'nickname' => $wxmember['nickname'],
                    'language' => $wxmember['language'],
                    'city' => $wxmember['city'],
                    'avatar' => $wxmember['headimgurl']
                ];
                $wxmemberObj->insert($aInput);
                session('open_id', $open_id);
            }*/

            return $this->fetch();
        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }


    //退出操作
    public function out()
    {

        try
        {
            cookie('student_phone', null);

            return json(['code' => 1, 'data' => '', 'msg' => '退出登录成功']);

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}