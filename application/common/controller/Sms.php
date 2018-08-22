<?php
namespace app\common\controller;

use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoiceVerifyCodeSender;
use Qcloud\Sms\SmsVoicePromptSender;
use Qcloud\Sms\SmsStatusPuller;
use Qcloud\Sms\SmsMobileStatusPuller;
use think\Exception;

class Sms
{
    // 短信应用SDK AppID
    protected static $appid = 1400102608; // 1400开头

    // 短信应用SDK AppKey
    protected static $appkey = "588a0c28893ead94152e90f07aed6163";

    // 签名
    protected static $smsSign = "上海欧游"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`

    /**单发
     * @param $params
     * @param $phoneNumber
     * @return array
     */
    public static function sendOne($params, $phoneNumber)
    {
        try {
            $ssender = new SmsSingleSender(self::$appid, self::$appkey);
            $result = $ssender->send(0, "86", $phoneNumber,
                $params, "", "");
            $rsp = json_decode($result);
            return ['code'=> 1, 'data' => $rsp, 'msg' => ''];
        } catch(Exception $e) {
            return ['code'=> 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**根据模板ID单发
     * @param $templateId
     * @param $params
     * @param $phoneNumber
     * @return array
     */
    public static function sendOneByTel($templateId, $params, $phoneNumber)
    {
        // 指定模板ID单发短信
        try {
            $ssender = new SmsSingleSender(self::$appid, self::$appkey);
            $result = $ssender->sendWithParam("86", $phoneNumber, $templateId,
                $params, self::$smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result);
            return ['code'=> 1, 'data' => $rsp, 'msg' => ''];
        } catch(Exception $e) {
            return ['code'=> 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**群发
     * @param $params
     * @param $phoneNumbers
     * @return array
     */
    public static function sendAny($params, $phoneNumbers)
    {
        try {
            $msender = new SmsMultiSender(self::$appid, self::$appkey);
            $result = $msender->send(0, "86", $phoneNumbers,
                $params, "", "");
            $rsp = json_decode($result);
            return ['code'=> 1, 'data' => $rsp, 'msg' => ''];
        } catch(Exception $e) {
            return ['code'=> 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**根据模板ID群发
     * @param $templateId
     * @param $params
     * @param $phoneNumbers
     * @return array
     */
    public static function sendAnyByTel($templateId, $params, $phoneNumbers)
    {
        try {
            $msender = new SmsMultiSender(self::$appid, self::$appkey);
            $result = $msender->sendWithParam("86", $phoneNumbers,
                $templateId, $params, self::$smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result);
            return ['code'=> 1, 'data' => $rsp, 'msg' => ''];
        } catch(Exception $e) {
            return ['code'=> 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


}