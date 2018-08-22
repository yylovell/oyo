<?php

namespace app\admin\validate;

use think\Validate;

class NewsValidate extends Validate
{
    protected $rule = [
        ['title', 'require', '标题不能为空'],
        ['is_send', 'require', '是否推送不能为空'],
        ['time', 'require', '首播时间不能为空'],
        ['discrpte', 'require', '内容描述不能为空'],
    ];

}