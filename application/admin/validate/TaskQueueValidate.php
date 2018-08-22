<?php

namespace app\admin\validate;

use think\Validate;

class TaskQueueValidate extends Validate
{
    protected $rule = [
        ['task_id', 'require', '计划任务ID不能为空'],
        ['class_name', 'require', '类名不能为空'],
        ['method_name', 'require', '方法名不能为空'],
    ];

}