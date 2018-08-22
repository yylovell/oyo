<?php
namespace app\index\controller;

use app\common\controller\Sys;
use think\Controller;
use think\Exception;
use think\Lang;

class Log extends Controller
{
    /**
     * @var object 模型
     */
    protected $_lModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->_lModel = new \app\index\model\Log();

    }

    /*统计访问IP*/
    public function ip()
    {

        try {

            $ip = getIP();
            $now = time();

            $aData = $this->_lModel->where(['ip' => $ip])->order('last_time desc')->limit(1)->select();
            if ($aData) {
                $aData = json_decode($aData[0], true);
                $time_diff = date('Y-m-d', $now) > date('Y-m-d', $aData['last_time']);
                if (!$time_diff) {//访问间隔不超过一天的累加次数
                    $count = $aData['count'] + 1;
                    $this->_lModel->update(['id' => $aData['id'], 'count' => $count, 'last_time' => $now]);

                    return json(['code' => 1, 'data' => '', 'msg' => '成功']);
                }

            }

            $arr = getCity($ip);

            $aInput = ['ip' => $arr['ip'], 'city' => $arr['city'], 'isp' => $arr['isp'], 'count' => 1, 'last_time' => $now];

            $this->_lModel->addLog($aInput);

            return json(['code' => 1, 'data' => '', 'msg' => '成功']);

        } catch (Exception $e) {
            trace($e->getMessage());
        }
    }

}