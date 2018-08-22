<?php
namespace app\index\controller;

use think\Exception;

class Cases extends Base
{
    /**
     * @var string 案例ID
     */
    protected $_Id = '';

    /**
     * @var object 案例模型
     */
    protected $_cModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->_cModel = new \app\index\model\Cases();

        if (in_array(ACTION_NAME, ['detail'])) {
            $this->_Id = input('id', 0, 'intval');
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
     * 案例列表
     * @return mixed
     */
    public function lists()
    {
        try {

            $ccModel = new \app\common\model\Casecat();
            $aData = $ccModel->getCaseCat();
            $type_name_map = [];
            foreach ($aData as $v){
                $type_name_map[$v['id']] = $v;
            }
            $this->assign('type_name_map', $type_name_map);

            $aDisLists = $this->_cModel->order(['photo1', 'time' => 'desc', 'id' => 'desc'])->select();

            $this->assign('cases_lists', $aDisLists);

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

            $aData = $this->_cModel->where(['id' => $this->_Id])->select();
            $aData = json_decode($aData[0],true);

            return $this->fetch('detail',['one'=>$aData]);

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
        }
    }
}