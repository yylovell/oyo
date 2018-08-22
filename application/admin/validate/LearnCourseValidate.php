<?php

namespace app\admin\validate;

use think\Validate;

class LearnCourseValidate extends Validate
{
    protected $rule = [
        ['title', 'require|max:20', '标题不能为空|标题最长20个字'],
        ['des', 'require', '内容描述不能为空'],
        ['photo', 'require', '必须上传封面图片'],
        ['long', 'require', '可选天数不能为空'],
        ['forward', 'require|between:1,30', '提前天数不能为空|提前天数需在1-30范围内']
    ];

}