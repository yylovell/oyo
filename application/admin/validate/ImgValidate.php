<?php

namespace app\admin\validate;

use think\Validate;

class ImgValidate extends Validate
{
    protected $rule = [
        ['photo', 'require', '图片路径不能为空']
    ];

}