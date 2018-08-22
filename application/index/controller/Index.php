<?php
namespace app\index\controller;

use app\common\controller\Sys;
use think\Exception;

class Index extends Base
{
    /**
     * @var string 案例ID
     */
    protected $_cId = '';

    /**
     * @var object 案例模型
     */
    protected $_cModel;

    /**
     * @var string 新闻ID
     */
    protected $_nId = '';

    /**
     * @var object 新闻模型
     */
    protected $_nModel;

    /**
     * @var object 轮播图模型
     */
    protected $_sModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->_sModel = new \app\admin\model\Slider();

        if (in_array(ACTION_NAME, ['index', 'cases'])) {
            $this->_cId = input('cid', 0, 'intval');
            $this->_cModel = new \app\index\model\Cases();
        }
        if (in_array(ACTION_NAME, ['index', 'news', 'mobile'])) {
            $this->_nId = input('nid', 0, 'intval');
            $this->_nModel = new \app\index\model\News();
        }
    }

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        try {
            // 服务简介
            $siModel = new \app\common\model\Serviceintr();
            $aServiceIntr = $siModel->getServiceIntr();
                // 两两组成一个数组
            $siData = [];
            $t = 0;
            $k = 0;
            foreach ($aServiceIntr as $v) {
                $siData[$k][$t] = $v;
                $t++;
                if ($t > 3) {
                    $t = 0;
                    $k++;
                }
            }

            $this->assign('serviceintr', $siData);

            // 轮播图
            $sData = $this->_sModel->where(['is_send' => Sys::NEWS_IS_SEND])->order(['weight_val' => 'asc', 'update_time' => 'desc'])->select();

            // 案例
            $cData = $this->_cModel->where(['is_classical' => Sys::CASES_IS_CLASSICAL])->order(['time' => 'desc'])->select();

            $ccModel = new \app\common\model\Casecat();
            $aData = $ccModel->getCaseCat();
            $type_name_map = [];
            foreach ($aData as $v){
                $type_name_map[$v['id']] = $v;
            }
            $this->assign('type_name_map', $type_name_map);

            // 新闻

            $nData = $this->_nModel->field('id, title, time, discrpte, link, is_send, photo, photo_thumb')->where(['is_send' => Sys::NEWS_IS_SEND])->order(['p_order' => 'desc', 'time' => 'desc'])->limit(4)->select();
            $one = $nData[0];

            $aOutput = [];
            if ($nData) {
                $mouth = $this->_getMouth();
                foreach ($nData as $k => $v) {
                    if ($k != 0) {
                        $v['year'] = substr($v['time'], 0, 4);
                        $v['mouth'] = $mouth[substr($v['time'], 5, 2)];
                        $v['day'] = substr($v['time'], 8, 2);
                        $aOutput[$k] = $v;
                    }
                }
            }

            // 新闻图
            $imgModel = new \app\admin\model\Img();
            $newsPhoto = $imgModel->getPhoto(['type' => Sys::IMG_IS_NEWS]);
            $this->assign('newsPhoto', $newsPhoto);

            // 合作伙伴图
            $pc_partner = $imgModel->getPhoto(['type' => Sys::IMG_IS_PARTNER, 'mobile' => Sys::IS_PC]);
            $this->assign('pcPhoto', $pc_partner);

            $mb_partner = $imgModel->getPhoto(['type' => Sys::IMG_IS_PARTNER, 'mobile' => Sys::IS_MOBILE]);
            $this->assign('mbPhoto', $mb_partner);

            // 大事记
            $eventModel = new \app\common\model\Event();
            $events = $eventModel->getEvents();
            foreach ($events as &$event) {
                $event['des'] = explode('|', $event['des']);
                $timeArr = $this->_formartTime($event['time']);
                $event['time'] = $timeArr['time'];
                $event['time_des'] = $timeArr['date'];
            }
            unset($event);

            $this->assign('events', $events);

            $this->assign('slider', $sData);
            $this->assign('cases', $cData);
            $this->assign('news', $aOutput);
            $this->assign('one', $one);

            return $this->fetch();

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
        }

    }

    /**
     * 格式化大事记时间节点
     * @param $time
     * @return array
     */
    private function _formartTime($time)
    {
        $timeArr = explode('-', $time);

        if (substr($timeArr[1], 0, 1) == '0') {
            $timeArr[1] = substr($timeArr[1], 1);
        }
        $date = $timeArr[0] . '年' . $timeArr[1] . '月';
        $time = $timeArr[0] . '.' . $timeArr[1];
        return ['time' => $time, 'date' => $date];

    }

    /*手机iframe(弃用)*/
    public function mobile()
    {
        $mouth = $this->_getMouth();
        $nData = $this->_nModel->where(['is_send' => Sys::NEWS_IS_SEND])->order(['time' => 'desc'])->select();
        $aOutput = [];
        foreach ($nData as $k => $v) {
            $v['year'] = substr($v['time'], 0, 4);
            $v['mouth'] = $mouth[substr($v['time'], 5, 2)];
            $v['day'] = substr($v['time'], 8, 2);
            $aOutput[$k] = $v;
        }
        $this->assign('news_mobile', $aOutput);

        return $this->fetch();
    }

    /**
     * 中英文切换
     * （弃用）
     */
    public function lang()
    {

        switch (input('lan')) {
            case 'cn':
                cookie('think_var', 'zh-cn');
                break;
            case 'en':
                cookie('think_var', 'en-us');
                break;
        }
    }

    /*测试页面*/
    public function test()
    {
        return $this->fetch();
    }

    /*
     * 获取月份
     */
    public function _getMouth()
    {
        return ['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
    }


}
