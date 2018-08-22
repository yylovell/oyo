<?php
namespace app\index\controller;

use app\common\controller\Sys;

class Join extends Base
{

    /**
     * @var object 职位模型
     */
    protected $_jModel;

    /**
     * @var object 福利模型
     */
    protected $_bModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();

        if (in_array(ACTION_NAME, ['lists'])) {
            $this->_jModel = new \app\admin\model\Join();
            $this->_bModel = new \app\common\model\Benefit();
        }

    }

    /**
     * 默认页
     */
    public function index()
    {
        $this->redirect('lists');
    }

    /**
     * 列表页
     * @return mixed
     */
    public function lists()
    {
        $aDisLists = $this->_jModel->where(['is_send' => Sys::NEWS_IS_SEND])->order(['update_time' => 'desc'])->select();

        $this->assign('join_lists', $aDisLists);

        $aBenefit = $this->_bModel->getBenefit();
        // 两两组成一个数组
        $aData = [];
        $t = 0;
        $k = 0;
        foreach ($aBenefit as $v) {
            $aData[$k][$t] = $v;
            $t++;
            if ($t > 1) {
                $t = 0;
                $k++;
            }
        }
        $this->assign('benefit_lists', $aData);

        return $this->fetch();
    }

}
