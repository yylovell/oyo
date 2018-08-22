<?php

namespace app\admin\validate;

use think\Validate;

class LearnConfigValidate extends Validate
{
    protected $rule = [
        ['type', 'require', '星期几不能为空'],
        ['learn_id', 'require', '请选择一个课程'],
        ['start_at', 'require', '时间不能为空'],
        ['end_at', 'require', '时间不能为空']
    ];

}