<?php

namespace app\admin\validate;

use think\Validate;

class CasesValidate extends Validate
{
    protected $rule = [
        ['title', 'require', '标题不能为空'],
        ['customer', 'require', '植入品牌不能为空'],
        ['product', 'require', '植入产品不能为空'],
        ['platform', 'require', '首播平台不能为空'],
        ['actors', 'require', '演员不能为空'],
        ['time', 'require', '首播时间不能为空'],
        ['content', 'require', '内容描述不能为空'],
        ['photo', 'require', '必须上传封面'],
        ['photo1', 'require', '必须上传详情图1'],
        ['photo2', 'require', '必须上传详情图2'],
        ['photo3', 'require', '必须上传详情图3'],
        ['photo4', 'require', '必须上传详情图4'],
    ];

}