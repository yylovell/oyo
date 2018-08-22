<?php

namespace app\admin\validate;

use think\Validate;

class SliderValidate extends Validate
{
    protected $rule = [
        ['is_send', 'require', '是否推送不能为空'],
        ['type', 'require', '类型不能为空'],
        ['url', 'require', '链接不能为空'],
        ['photo','require', '轮播图片不能为空'],
        ['weight_val', 'require|number', '权值不能为空|权值必须为数字']
    ];

}