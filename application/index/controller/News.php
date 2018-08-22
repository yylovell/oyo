<?php
namespace app\index\controller;

use app\common\controller\Sys;
use think\Exception;

class News extends Base
{
    /**
     * @var string 新闻ID
     */
    protected $_Id = '';

    /**
     * @var object 新闻模型
     */
    protected $_nModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->_nModel = new \app\index\model\News();

        if (in_array(ACTION_NAME, ['detail'])) {
            $this->_Id = input('id');
        }

    }

    /*
     * 默认页
     */
    public function index()
    {
        $this->redirect('lists');
    }

    /**
     * 新闻列表
     * @return mixed
     */
    public function lists()
    {
        try {
            $mouth = $this->_getMouth();

            $aDisLists = $this->_nModel->field('id, title, time, discrpte, link, is_send')->order(['p_order' => 'desc', 'time' => 'desc'])->paginate(5);
            $page = $aDisLists->render();

            $nData = [];
            foreach ($aDisLists as $k => $v) {
                $v['year'] = substr($v['time'], 0, 4);
                $v['mouth'] = $mouth[substr($v['time'], 5, 2)];
                $v['day'] = substr($v['time'], 8, 2);
                $nData[$k] = $v;
            }

            /*所有的推荐*/
            $all = $this->_nModel->where(['is_send' => Sys::NEWS_IS_SEND])->field('id, title, time, discrpte, link, is_send')->order(['p_order' => 'desc', 'time' => 'desc'])->select();
            $sData = [];
            foreach ($all as $k => $v) {
                $v['year'] = substr($v['time'], 0, 4);
                $v['mouth'] = $mouth[substr($v['time'], 5, 2)];
                $v['day'] = substr($v['time'], 8, 2);
                $sData[$k] = $v;
            }
            $this->assign('send_lists', $sData);

            $this->assign('news_lists', $nData);
            $this->assign('page', $page);

            return $this->fetch();

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
        }
    }

    /*
     * 详情
     */
    public function detail()
    {
        try {
            $mouth = $this->_getMouth();

            $aData = $this->_nModel->where(['id' => $this->_Id])->select();

            $nData = [];
            foreach ($aData as $k => $v) {
                $v['year'] = substr($v['time'], 0, 4);
                $v['mouth'] = $mouth[substr($v['time'], 5, 2)];
                $v['day'] = substr($v['time'], 8, 2);
                $nData[$k] = $v;
            }

            $nData = json_decode($nData[0],true);
            return $this->fetch('detail',['one'=>$nData]);

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
        }
    }

    /*
     * 获取月份
     */
    public function _getMouth()
    {
        return ['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
    }
}