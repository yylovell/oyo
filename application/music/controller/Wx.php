<?php
namespace app\music\controller;

use think\Controller;

class Wx extends Controller
{

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        //获得参数 signature nonce token timestamp echostr
        $nonce = $_GET['nonce'];
        $token = 'weixin';
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = [$nonce, $timestamp, $token];
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
            //第一次接入weixin api接口的时候
            echo $echostr;
            exit;
        } else {
            $this->responseMsg();
            $this->defineItem();
        }
    }

    // 接收事件推送并回复
    public function responseMsg()
    {
        //1.获取到微信推送过来post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.处理消息类型，并设置回复类型和内容
        /*<xml>发送者为微信用户
        <ToUserName><![CDATA[toUser]]></ToUserName>--公众号名
        <FromUserName><![CDATA[FromUser]]></FromUserName>--微信用户名
        <CreateTime>123456789</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[subscribe]]></Event>
        </xml>*/
        $postObj = simplexml_load_string($postArr);

        //toUser为微信用户，fromUser为公众账号
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        //实例化对象
        /*$responseObj = ;*/
        //判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注 subscribe 事件
            if (strtolower($postObj->Event == 'subscribe')) {
                //回复用户消息(纯文本格式)
                $content = '好运来蛋糕屋，欢迎您，使用帮助，请输入help';
                $returnContent = \app\common\controller\Wx::responseText($toUser, $fromUser, $content);
                echo $returnContent;

            }
            //重扫码事件触发
            if (strtolower($postObj->Event == 'SCAN')) {
                if (strtolower($postObj->EventKey == '2500')) {
                    $content = '临时扫码事件';
                    $this->sendTemplateMsg();//调用模板消息
                }
                if (strtolower($postObj->EventKey == '3000')) {
                    $content = '永久扫码事件';
                }
                $returnContent = \app\common\controller\Wx::responseText($toUser, $fromUser, $content);
                echo $returnContent;
            }
            //自定义菜单事件点击
            if (strtolower($postObj->Event == 'CLICK')) {
                if (strtolower($postObj->EventKey == 'user')) {
                    $content = '这是个人中心';
                }
                if (strtolower($postObj->EventKey == 'activity')) {
                    $content = '这是活动';
                }
                $returnContent = \app\common\controller\Wx::responseText($toUser, $fromUser, $content);
                echo $returnContent;
            }
            if (strtolower($postObj->Event == 'VIEW')) {
                $content = 'url' . $postObj->EventKey;
                $returnContent = \app\common\controller\Wx::responseText($toUser, $fromUser, $content);
                echo $returnContent;
            }
        }

        //用户发送关键字的时候，公众账号回复
        if (strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == '4') {
            //注意：进行多图文发送时，子图文个数不能超过10个
            $arr = [
                [
                    'title' => '鹿透社',
                    'description' => "家乡",
                    'picUrl' => '',
                    'url' => 'http://www.yylovell.cn',
                ],
                [
                    'title' => 'music go!',
                    'description' => "一万小时的音乐之旅",
                    'picUrl' => '',
                    'url' => 'http://music.yylovell.cn',
                ],
                [
                    'title' => '会员中心',
                    'description' => "方便、快捷",
                    'picUrl' => '',
                    'url' => 'http://music.yylovell.cn/student',
                ],
            ];
            $returnContent = \app\common\controller\Wx::responseNews($toUser, $fromUser, $arr);
            echo $returnContent;


        } else if (strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == '5') {
            $arr = [
                [
                    'title' => '鹿透社',
                    'description' => "家乡",
                    'picUrl' => '',
                    'url' => 'http://www.yylovell.cn',
                ]
            ];
            $returnContent = \app\common\controller\Wx::responseNews($toUser, $fromUser, $arr);
            echo $returnContent;
        } else {
            if (trim($postObj->Content) == '6') {//上海天气
                $url = 'https://api.thinkpage.cn/v3/weather/now.json?key=ozytdx8sjl3b9jqa&location=shanghai&language=zh-Hans&unit=c';
                $output = \app\common\controller\Wx::responseAPI($url);
                foreach ($output as $value) {
                    foreach ($value as $v) {
                        $content = "上海天气：" . $v['now']['text'] . "\n" . "当前温度：" . $v['now']['temperature'] . "\n" . "相对湿度：" . $v['now']['humidity'] . "\n" . "风向：" . $v['now']['wind_direction'];
                    }
                }
                $returnContent = \app\common\controller\Wx::responseText($toUser, $fromUser, $content);
                echo $returnContent;

            } else {
                switch (trim($postObj->Content)) {
                    case 1:
                        $content = 'tel:13965649589';
                        break;
                    case 2:
                        $content = '鹿透社';
                        break;
                    case 3:
                        $content = '<a href=\'http://www.yylovell.cn\'>主页</a>';
                        break;
                    case 'help':
                        $content = 'please call the Number:' . "\n" . '1-联系我们' . "\n" . '2-店名' . "\n" . '3-商城主页' . "\n" . '4-多图文' . "\n" . '5-单图文' . "\n" . '6-上海即时天气情况';
                        break;
                    /*default:
                        $content = '未知指令，输入help查看所有指令';
                        break;*/
                }
                $returnContent = \app\common\controller\Wx::responseText($toUser, $fromUser, $content);
                echo $returnContent;
            }

        }//if end
    }//reponseMsg end

    public function defineItem()
    {
        //创建微信菜单
        /*$responseObj = D("Index");*/
        $access_token = \app\common\controller\Wx::getWxAccessToken();

        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token;
        $postJson = '{
     "button":[
     {
          "type":"view",
          "name":"网页授权",
          "url":"http://music.yylovell.cn/Wx/getUserDetail"
      },
      {
           "type":"view",
            "name":"music go !",
            "url":"http://music.yylovell.cn"
       },
      {
            "type":"view",
            "name":"个人中心",
            "url":"http://music.yylovell.cn/student"
      }
     ]
 }';
        $res = \app\common\controller\Wx::responseAPI($url, 'post', 'json', $postJson);
        dump($res);
    }


    //usetSession[''access_token]
    public function unsetSession()
    {
        if ($_SESSION['access_token']) {
            unset($_SESSION['access_token']);
            echo $_SESSION['access_token'] ? $_SESSION['access_token'] : '无';
        } else {
            echo '无';
        }

    }

    //上传图文
    public function sendNews()
    {
        /*$responseObj = D("Index");*/
        $access_token = \app\common\controller\Wx::getWxAccessToken();
        $type = 'image';
        $thumb_media_id = \app\common\controller\Wx::sendPhoto($access_token, $type);
        //echo $access_token;
        //echo "<hr>";
        echo $thumb_media_id;
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=" . $access_token;
        $postJson = '{
                    "articles":
                   [
		            {
                        "thumb_media_id":"' . $thumb_media_id . '",
                        "author":"someone",
			             "title":"鹿透社",
			             "content_source_url":"www.qq.com",
			             "content":"<h1>家乡的味道</h1>",
			             "digest":"this is des",
                        "show_cover_pic":1
		             }
                    ]
                }';

        $res = \app\common\controller\Wx::responseAPI($url, 'post', 'json', $postJson);
        dump($res);

    }

    //群发
    public function sendMsgAll()
    {
        /*$responseObj = D("Index");*/
        $access_token = \app\common\controller\Wx::getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=' . $access_token;
        $postJson = '{
    "touser":"odhT_wn0pp53EbxFKRKNwe7HAlRw",
    "text":{
           "content":"你好"
           },
    "msgtype":"text"
}';
        $res = \app\common\controller\Wx::responseAPI($url, 'post', 'json', $postJson);
        dump($res);
    }

    //网页授权

    public static function getUserDetail()
    {
        header('content-type:text/html;charset=utf-8');
        //1获取code
        $appid = 'wx66bfe6a77a31a2f9';
        $redirect_uri = urlencode('http://music.yylovell.cn/Wx/getUserInfo');
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=snsapi_userinfo&state=yy#wechat_redirect";
        header('location:' . $url);
    }

    public function getUserInfo()
    {
        header('content-type:text/html;charset=utf-8');
        //2获取网页授权的access_token
        /*$responseObj = D("Index");*/
        $appid = 'wx66bfe6a77a31a2f9';
        $appsecret = '11255b050bb8199c37ffb5cc1740a936';
        $code = $_GET['code'];
        // echo $code;
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $appsecret . "&code=" . $code . "&grant_type=authorization_code";
        $res = \app\common\controller\Wx::responseAPI($url, 'get');
        $openid = $res['openid'];
        $access_token = $res['access_token'];
        $info_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $info_res = \app\common\controller\Wx::responseAPI($info_url, 'get');
        session('wxmember', $info_res);
        header('location:' . "http://music.yylovell.cn/login");
    }

    //模板消息
    public function sendTemplateMsg($username, $order_money)
    {
        //1获取access_token
        /*$responseObj = D("Index");*/
        $access_token = \app\common\controller\Wx::getWxAccessToken();
        //echo $access_token;
        //echo "<hr>";
        $name = $username;
        $money = $order_money;
        $data = '欢迎再次购买';
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $postJson = '{
           "touser":"odhT_wn0pp53EbxFKRKNwe7HAlRw",
           "template_id":"3TMG0S4PgsIGVFpL3al9Zd5VgECQSBdfrbL-ZoU9xRE",
           "url":"http://yylovell.cn/lucky/index.php/User/onesOrder",
           "data":{
                   "name": {
                       "value":"' . $name . '",
                       "color":"#173177"
                   },
                   "money":{
                       "value":"' . $money . '",
                       "color":"#173177"
                   },
                   "data":{
                       "value":"' . $data . '",
                       "color":"#f45675"
                   }
           }
       }';
        $res = \app\common\controller\Wx::responseAPI($url, 'post', 'json', $postJson);
        dump($res);
    }

    //分享
    public function share()
    {
        //1获取jsapi_ticket
        /*$responseObj = D("Index");*/
        $jsapi_ticket = \app\common\controller\Wx::getJsApiTicket();

        $url = "http://music.yylovell.cn/Wx/share";
        $timestamp = time();
        $noncestr = \app\common\controller\Wx::getRandCode();
        $signature = "jsapi_ticket=" . $jsapi_ticket . "&noncestr=" . $noncestr . "&timestamp=" . $timestamp . "&url=" . $url;
        $signature = sha1($signature);

        if ($signature) {
            //echo $signature;
            //echo '<hr>';
        } else {
            echo '<span style="color:red">未生成</span>';
            echo '<hr>';
        }


        $this->assign('timestamp', $timestamp);
        $this->assign('noncestr', $noncestr);
        $this->assign('signature', $signature);
        //$this->display('Cake/cakeInfo');

    }

    //生成临时二维码
    public function getQrcode()
    {
        header('content-type:text/html;charset=utf-8');
        //1获取ticket
        /*$responseObj = D("Index");*/
        $ticket = \app\common\controller\Wx::getQrTicket();

        //2使用ticket获取二维码图片
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $ticket;
        echo "<h1>临时二维码</h1><img src=" . $url . " />";
    }

    //生成永久二维码
    public function getForeverQrcode()
    {
        header('content-type:text/html;charset=utf-8');
        //1获取ticket
        /*$responseObj = D("Index");*/
        $ticket = \app\common\controller\Wx::getForeverQrTicket();

        //2使用ticket获取二维码图片
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $ticket;
        echo "<h1>永久二维码</h1><img src=" . $url . " />";
    }


}