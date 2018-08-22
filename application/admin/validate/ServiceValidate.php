<?php

namespace app\admin\validate;

use think\Validate;

class ServiceValidate extends Validate
{
    protected $rule = [
        ['title', 'require', '标题不能为空'],
        ['des', 'require|max:1000', '描述不能为空|描述最长不得超过1000个字符'],
        ['tag', 'require|max:4', '标签背景不能为空|标签背景长度不得超过4个字符'],
        ['weight_val', 'require|number', '权值不能为空|权值必须为数字']
    ];

}