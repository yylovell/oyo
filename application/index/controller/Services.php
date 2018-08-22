<?php
namespace app\index\controller;


class Services extends Base
{
    /**
     * @var object 模型
     */
    protected $_sModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->_sModel = new \app\common\model\Service();
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
        $aData = $this->_sModel->getService();
        $this->assign('data', $aData);
        return $this->fetch();
    }

}
