<?php

namespace app\admin\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        ['username', 'unique:user', '管理员已经存在'],
        ['phone', 'is_mobile:thinkphp', '手机号格式错误']
    ];

    protected function is_mobile($phone)
    {
        return is_mobile($phone);
    }

}