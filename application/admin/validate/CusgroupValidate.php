<?php

namespace app\admin\validate;

use think\Validate;

class CusgroupValidate extends Validate
{
    protected $rule = [
        ['name', 'require', '名称不能为空'],
    ];

}