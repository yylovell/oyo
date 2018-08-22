<?php

namespace app\task\controller;

use app\common\controller\Sms;
use think\Exception;

/**
 * 短信计划任务控制类
 *
 * @category	Controller
 * @package	app\task\controller
 */
class Smstask extends Base
{

    /**
     * 初始化
     * @access protected
     * @return void
     */
    protected function _initialize()
    {
        // 设置运行限制时间
        set_time_limit(0);
        // 忽略用户中断
        ignore_user_abort(true);
    }

    /**
     * 每日检查是否有课程，并给管理员发短信
     * @access public
     * @return void
     */
    public function vipSms()
    {
        try
        {
            $vModel = new \app\common\model\Vipstudent();
            $aData = $vModel->getTodayData();

            if (empty($aData))
            {
                $sms = '暂无，';
            }
            else
            {
                $sms = '';
                foreach ($aData as $k=>$item)
                {
                    $sms .= $k . ' ,课程等级：';
                    foreach ($item as $v)
                    {
                        $sms .= $v[0]['grade']['name'] . ' ,';
                    }
                }
            }

            // 发送短信
            $telId = 144731; //短信模板ID，参见腾讯云
            $params = [$sms];

            $userModel = new \app\admin\model\UserModel();
            $aData = $userModel->where(['typeid'=> 1])->field('phone')->select()->toArray();
            $phoneNumbers = array_column($aData, 'phone');

            if (empty($phoneNumbers))
            {
                $phoneNumbers = ['15921632040'];
            }

            // 根据模板群发给管理员角色下的所有手机号
            $res = Sms::sendAnyByTel($telId, $params, $phoneNumbers);
            if ($res['code'] = 0)
            {
                throw new Exception($res['msg']);
            }

        }
        catch (Exception $ex)
        {
            $this->logError("任务出错，错误信息：{$ex->getMessage()}");
        }
    }
}
