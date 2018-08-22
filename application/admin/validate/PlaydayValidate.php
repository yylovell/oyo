<?php

namespace app\admin\validate;

use think\Validate;

class PlaydayValidate extends Validate
{
    protected $rule = [
        ['learn_id', 'require', '请选择一个课程'],
        ['date', 'require', '日期不能为空'],
    ];

}