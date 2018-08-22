<?php

namespace app\admin\validate;

use think\Validate;

class ServiceIntrValidate extends Validate
{
    protected $rule = [
        ['title', 'require', '标题不能为空'],
        ['des', 'require|max:100', '描述不能为空|描述最长不得超过100个字符'],
        ['tag', 'require', '图标不能为空'],
        ['weight_val', 'require|number', '权值不能为空|权值必须为数字']
    ];

}