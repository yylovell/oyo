<?php

namespace app\admin\validate;

use think\Validate;

class EventValidate extends Validate
{
    protected $rule =   [
        'time'  => 'require',
        'des'   => 'require|max:100'
    ];

    protected $message  =   [
        'time.require'          => '时间节点不能为空',
        'des.require'           => '节点描述不能为空',
        'des.max'               => '节点描述最多100字',
    ];

}