<?php

namespace app\admin\validate;

use think\Validate;

class StudentsValidate extends Validate
{
    protected $rule = [
        ['name', 'require', '姓名不能为空'],
        ['phone', 'require', '手机号不能为空'],
        ['sign_time', 'require', '注册时间不能为空']
    ];

}