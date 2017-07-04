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

//路由配置文件
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    // 以前访问地址 http://tp5.cn/home/index/index                home模块 index 控制器 index操作    对应文件 D:\WWW\tp5\core\app\home\controller\Index.php
    // 配置路由后 现在访问地址：http://tp5.cn/hello/1             home模块 index 控制器 index操作    对应文件 D:\WWW\tp5\core\app\home\controller\Index.php
    '[hello]'     => [
        ':id'   => ['index/index', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'get']],
    ],
];
