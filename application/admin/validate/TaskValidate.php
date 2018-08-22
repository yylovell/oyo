<?php

namespace app\admin\validate;

use think\Validate;

class TaskValidate extends Validate
{
    protected $rule = [
        ['name', 'require', '计划名称不能为空'],
        ['class_name', 'require', '类名不能为空'],
        ['method_name', 'require', '方法名不能为空'],
        ['type', 'require', '类型不能为空'],
    ];

}