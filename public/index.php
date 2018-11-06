<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define('PUBLIC_PATH', __DIR__);

//500错误日志
define('ERROR_LOG_PATH', __DIR__ . '/../error_log/');

//前端资源地址
define('VIEW_STATIC_PATH', '/static');
define('VIEW_IMAGE_PATH', VIEW_STATIC_PATH.'/img/');
define('VIEW_FILE_PATH', VIEW_STATIC_PATH.'/file');
define('VIEW_FONTS_PATH', VIEW_STATIC_PATH.'/fonts');

//后端资源地址
define('STATIC_PATH', PUBLIC_PATH . VIEW_STATIC_PATH);
define('IMAGE_PATH', PUBLIC_PATH . VIEW_IMAGE_PATH);
define('FILE_PATH', PUBLIC_PATH . VIEW_FILE_PATH);
define('FONTS_PATH', PUBLIC_PATH . VIEW_FONTS_PATH);

//用户默认头像
define('DEFAULT_IMAGE', VIEW_IMAGE_PATH . '/user_icon/default.jpg');

//厂商家默认LOGO
define('DEFAULT_LOGO', VIEW_IMAGE_PATH . '/logo/default.png');

require __DIR__ . '/../thinkphp/start.php';
