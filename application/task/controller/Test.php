<?php

namespace app\task\controller;

use app\common\controller\Sms;
use think\Exception;

/**
 * 每日任务控制类
 *
 * @category	Controller
 * @package	app\task\controller
 */
class Test extends Base
{

    /**
     * 初始化
     * @access protected
     * @return void
     */
    protected function _initialize()
    {
        // 设置运行限制时间
        //set_time_limit(0);
        // 忽略用户中断
        ignore_user_abort(true);
    }

    /**
     * 任务入口
     * @access public
     * @return void
     */
    public function index()
    {
        try
        {
            $this->stats();
        }
        catch (Exception $ex)
        {
            $this->logError("任务出错，错误信息：{$ex->getMessage()}");
        }
    }

    /**
     *
     * @access public
     * @return void
     */
    public function stats()
    {
        // 发送短信
        $telId = 144731;
        $params = '2018-06-23 14:30:30 课程等级C1,';
        //throw new Exception($params);
        $phoneNumbers = ['13661524424'];
        $strAppKey = "5f03a35d00ee52a21327ab048186a2c4"; //sdkappid 对应的 appkey，需要业务方高度保密
        $strRand = $this->getRandom(); //url 中的 random 字段的值
        $strTime = time(); //unix 时间戳
        $sig = $this->calculateSigForTemplAndPhoneNumbers($strAppKey, $strRand, $strTime, $phoneNumbers);

        $res = Sms::sendOneByTel($telId, $params, $phoneNumbers);
        if ($res['code'] > 0)
        {
            throw new Exception($res['data']);
        }
        else
        {
            throw new Exception($res['msg']);
        }

    }

    /**
     * 生成随机数
     *
     * @return int 随机数结果
     */
    public function getRandom()
    {
        return rand(100000, 999999);
    }

    /**
     * 生成签名
     *
     * @param string $appid         sdkappid
     * @param string $appkey        sdkappid对应的appkey
     * @param string $curTime       当前时间
     * @param array  $phoneNumbers  手机号码
     * @return string  签名结果
     */
    public function calculateSigForTemplAndPhoneNumbers($appkey, $random,
                                                        $curTime, $phoneNumbers)
    {
        $phoneNumbersString = $phoneNumbers[0];
        for ($i = 1; $i < count($phoneNumbers); $i++) {
            $phoneNumbersString .= ("," . $phoneNumbers[$i]);
        }

        return hash("sha256", "appkey=".$appkey."&random=".$random
            ."&time=".$curTime."&mobile=".$phoneNumbersString);
    }

    public function test()
    {
        $file = fopen(ROOT_PATH . 'public/news/test.txt', 'a');
        $txt = "测试信息,定时运行， 时间是：" . date('Y-m-d H:i:s', time());
        fwrite($file, $txt);
        fclose($file);
    }
}
