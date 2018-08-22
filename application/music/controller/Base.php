<?php
namespace app\music\controller;

use app\common\controller\Sys;
use think\Controller;

class Base extends Controller
{
    protected $_student;

    public function _initialize()
    {

        parent::_initialize();
        header("content-type:text/html;charset=utf-8");

        if(empty(cookie('student_phone'))){

            $this->redirect(url('login/index'));
        }
        $studentModel = new \app\common\model\Students();
        $student = $studentModel -> getByPhone(cookie('student_phone'));
        if ($student['is_send'] == Sys::NEWS_NOT_SEND)//禁止登陆
        {
            $this->redirect(url('login/index'));
        }
        $head_name = mb_substr($student['name'], 0, 1);

        $this->assign('head_name', $head_name);
        $this->assign('student_info', $student);

        $this->_student = $student;

    }
}