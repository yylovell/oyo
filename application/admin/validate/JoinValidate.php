<?php

namespace app\admin\validate;

use think\Validate;

class JoinValidate extends Validate
{
    protected $rule = [
        ['name', 'require', '职位名称不能为空'],
        ['des', 'require', '职位描述不能为空'],
        ['address', 'require', '工作地不能为空']
    ];

}