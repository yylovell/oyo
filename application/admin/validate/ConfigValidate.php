<?php

namespace app\admin\validate;

use think\Validate;

class ConfigValidate extends Validate
{
    protected $rule =   [
        'title'  => 'require|max:80',
        'seo_keywords'   => 'require|max:100',
        'seo_des' => 'require|max:200',
        'slogan' => 'require',
        'email' => 'email',
        'tel' => 'require',
        'address' => 'require'
    ];

    protected $message  =   [
        'title.require'         => '网站标题不能为空',
        'name.max'              => '网站标题最多80字',
        'seo_keywords.require'  => 'seo关键字不能为空',
        'seo_keywords.max'      => 'seo关键字最多100字',
        'seo_des.require'       => 'seo描述不能为空',
        'seo_des.max'           => 'seo描述最多200字',
        'slogan.require'        => '网站简介不能为空',
        'email'                 => '邮箱格式错误',
        'tel'                   => '电话不能为空',
        'address'               => '地址不能为空'
    ];

}