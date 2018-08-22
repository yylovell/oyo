<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'detail' => 'detail/id/',

    '__pattern__' => [
        'name' => '\w+',
    ],
    '__domain__'=>[
        'www'        => 'index',
        'admin'      => 'admin',
        'api'        => 'api',
        'music'      => 'music',
        'task'       => 'task',
        'cus'        => 'cus',
        // 泛域名规则建议在最后定义
        '*'          => 'index'

    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
