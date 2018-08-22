<?php

namespace app\admin\validate;

use think\Validate;

class CusserviceValidate extends Validate
{
    protected $rule = [
        ['user_name', 'require', '名称不能为空'],
        ['group_id', 'require', '请选择分组'],
    ];

}