<?php
namespace app\music\controller;

use app\common\controller\Sys;
use think\Exception;

class Student extends Base
{

    public $_sModel;
    public $_vModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_sModel = new \app\common\model\Students();
        $this->_vModel = new \app\common\model\Vipstudent();
    }

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        $courdeData = $this->_vModel
            ->alias('v')
            ->where('student_id', $this->_student['id'])
            ->join('oyo_learn_course lc', 'lc.id = v.learn_id')
            ->join('oyo_course_grade c', 'c.id = v.grade_id')
            ->field('v.*, lc.title, c.name')
            ->order('start_at')
            ->select();

        $now = date('Y-m-d H:i:s', time());
        $expireData = $nowData = [];
        foreach ($courdeData as $v)
        {
            $day = substr($v['start_at'], 0, 10);
            $start_at = substr($v['start_at'], 11, 5);
            $end_at = substr($v['end_at'], 11, 5);
            $v['time_area'] = $day . ' ' . $start_at . ' 至 '. $end_at;

            if ($v['start_at'] < $now)
            {
                $expireData[] = $v;
            }
            else
            {
                $nowData[] = $v;
            }
        }
        krsort($expireData);
        $this->assign(['nowData' => $nowData, 'expireData' => $expireData]);

        /*客服组别*/
        $kfgroup = db('cus_serv_groups')->find(1);
        $this->assign('kfgroup', $kfgroup);

        /*客服配置*/
        $kfconfig = db('kf_config')->find(1);
        $this->assign('kfconfig', $kfconfig);


        return $this->fetch();
    }

    /**
     * 修改密码
     * @return array|\think\response\Json
     */
    public function editpasswd()
    {
        try
        {
            $param = input();

            if (strlen($param['password']) < 4)
            {
                throw new Exception('密码长度不能小于4');
            }
            $student = $this->_sModel->getByPhone(cookie('student_phone'));
            if (empty($student))
            {
                throw new Exception('登录账户不存在, 或登录已失效, 请重新登录');
            }
            $arr = [
                'id' => $student['id'],
                'password' => md5($param['password'])
            ];

            $flag = $this->_sModel->replacePassword($arr);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 修改头像
     * @return array|\think\response\Json
     */
    public function editAvatar()
    {
        try
        {
            $param = input();

            $student = $this->_sModel->getByPhone(cookie('student_phone'));
            if (empty($student))
            {
                throw new Exception('登录账户不存在, 或登录已失效, 请重新登录');
            }
            $arr = [
                'id' => $student['id'],
                'user_avatar' => $param['avatar']
            ];

            $flag = $this->_sModel->updateAvatar($arr);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 取消预约
     */
    public function cancel()
    {
        try
        {
            $param = input();
            $vip_id = $param['vip_id'];

            if (!$vip_id)
            {
                throw new Exception('数据错误');
            }
            $flag = $this->_vModel->del($vip_id);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    // pc客户端
    public function chat()
    {
        // 跳转到移动端
        /*if(request()->isMobile()){
            $param = http_build_query([
                'id' => input('param.id'),
                'name' => input('param.name'),
                'group' => input('param.group'),
                'avatar' => input('param.avatar')
            ]);
            $this->redirect('mobile?' . $param);
        }*/

        $this->assign([
            'socket' => config('socket'),
            'id' => input('param.id'),
            'name' => input('param.name'),
            'group' => input('param.group'),
            'avatar' => input('param.avatar'),
        ]);

        return $this->fetch('mobile');
    }

    // 移动客户端
    public function mobile()
    {
        $this->assign([
            'socket' => config('socket'),
            'id' => input('param.id'),
            'name' => input('param.name'),
            'group' => input('param.group'),
            'avatar' => input('param.avatar'),
        ]);

        return $this->fetch();
    }

}