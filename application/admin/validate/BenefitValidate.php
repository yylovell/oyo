<?php

namespace app\admin\validate;

use think\Validate;

class BenefitValidate extends Validate
{
    protected $rule = [
        ['title', 'require', '标题不能为空'],
        ['des', 'require|max:15', '描述不能为空|描述最长不得超过15个字符'],
        ['tag', 'require', '图标不能为空']
    ];

}