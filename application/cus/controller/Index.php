<?php
namespace app\cus\controller;

class Index extends Base
{
    public function index()
    {
        // 客服信息
        $cusservice = new \app\common\model\Cusservice();
        $userInfo = $cusservice->where('id', cookie('l_user_id'))->find();

        $this->assign([
            'uinfo' => $userInfo,
            'word' => db('cus_words')->where('status', 1)->select(),
            'groups' => db('cus_serv_groups')->where('status', 1)->select(),
            'status' => db('kf_config')->where('id', 1)->find()
        ]);

        return $this->fetch();
    }

    // 获取服务用户列表
    // 此方法是为了防止客服工作期间错误的刷新工作台，导致服务人员消失的问题
    public function getUserList()
    {
        if(request()->isAjax()){

            // 此处只查询过去 三个小时 内的未服务完的用户
            $userList = db('cus_service_log')->field('user_id id,user_name name,user_avatar avatar,user_ip ip')
                ->where('kf_id', cookie('l_user_id'))
                ->where('start_time', '>', time() - 3600 * 3)->where('end_time', 0)->select();

            return json(['code' => 1, 'data' => $userList, 'msg' => 'ok']);
        }
    }

    // 获取聊天记录
    public function getChatLog()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = 10; // 一次显示10 条聊天记录
            $offset = ($param['page'] - 1) * $limit;

            $logs = db('cus_chat_log')->where(function($query) use($param){
                $query->where('from_id', $param['uid'])->where('to_id', 'KF' . cookie('l_user_id'));
            })->whereOr(function($query) use($param){
                $query->where('from_id', 'KF' . cookie('l_user_id'))->where('to_id', $param['uid']);
            })->limit($offset, $limit)->order('id', 'desc')->select();

            $total =  db('cus_chat_log')->where(function($query) use($param){
                $query->where('from_id', $param['uid'])->where('to_id', 'KF' . cookie('l_user_id'));
            })->whereOr(function($query) use($param){
                $query->where('from_id', 'KF' . cookie('l_user_id'))->where('to_id', $param['uid']);
            })->count();

            foreach($logs as $key=>$vo){

                $logs[$key]['type'] = 'user';
                $logs[$key]['time_line'] = date('Y-m-d H:i:s', $vo['time_line']);

                if($vo['from_id'] == 'KF' . cookie('l_user_id')){
                    $logs[$key]['type'] = 'mine';
                }
            }

            return json(['code' => 1, 'data' => $logs, 'msg' => intval($param['page']), 'total' => ceil($total / $limit)]);
        }
    }

    // ip 定位
    public function getCity()
    {
        $ip = input('param.ip');
        $ip = '42.156.138.96';

        $info = getCity($ip);

        if($info){
            return json(['code' => 1, 'data' => $info['city'], 'msg' => 'ok']);
        }else{

            return json(['code' => 1, 'data' => '', 'msg' => '无信息']);
        }
    }
}