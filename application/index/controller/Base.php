<?php
namespace app\index\controller;

use app\common\controller\Sys;
use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {

        parent::_initialize();

        $footModel = new \app\common\model\Footer();
        $footInfo = $footModel->find(['id' => 1]);
        $footInfo['slogan'] = explode('|', $footInfo['slogan']);
        $footInfo['address'] = explode('|', $footInfo['address']);
        $footInfo['company_hope'] = explode('|', $footInfo['company_hope']);
        $footInfo['company_mission'] = explode('|', $footInfo['company_mission']);
        $footInfo['company_belief'] = explode('|', $footInfo['company_belief']);
        $footInfo['key_value'] = explode('|', $footInfo['key_value']);

        $this->assign('foot', $footInfo);


        // 公众号二维码图
        $imgModel = new \app\admin\model\Img();
        $pubPhoto = $imgModel->getPhoto(['type' => Sys::IMG_IS_PUBLIC]);
        $this->assign('pubPhoto', $pubPhoto);

    }
}