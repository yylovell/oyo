<?php

namespace app\admin\validate;

use think\Validate;

class CaseCatValidate extends Validate
{
    protected $rule = [
        ['name', 'require', '分类名称不能为空'],
        ['weight_val', 'require|number', '权值不能为空|权值必须为数字']
    ];

}