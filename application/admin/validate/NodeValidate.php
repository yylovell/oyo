<?php

namespace app\admin\validate;

use think\Validate;

class NodeValidate extends Validate
{
    protected $rule = [
        ['node_name', 'require', '菜单名称不能为空'],
        ['control_name', 'require', '控制器名不能为空'],
        ['action_name', 'require', '方法名不能为空'],
        ['typeid', 'require', '父级ID不能为空'],
    ];

}