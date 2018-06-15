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

use \think\Route;

/**
 * 路由
 * @param :version string 版本号    v1 | v2
 */

//用户授权，自动注册
Route::post('api/:version/user', 'api/:version.User/create');
//查找用户数据
Route::get('api/:version/user', 'api/:version.User/select');
//修改用户数据
Route::put('api/:version/user', 'api/:version.User/update');
//删除用户数据
Route::delete('api/:version/user/:id', 'api/:version.User/delete');