<?php

namespace app\admin\validate;

use think\Validate;

class LearnStudentValidate extends Validate
{
    protected $rule = [
        ['learn_id', 'require', '体验课ID缺失'],
        ['name', 'require|max:12', '姓名不能为空|姓名最长12个字'],
        ['phone', 'require', '手机号必填'],
        ['start_at', 'require', '体验时间不能为空'],
        ['end_at', 'require', '体验时间不能为空']
    ];

}