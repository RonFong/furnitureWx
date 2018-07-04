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

Route::group('api/:version',function() {
    //用户授权，注册 | 更新
    Route::post('user', 'api/:version.User/saveUser');
    //查找用户数据
    Route::get('user', 'api/:version.User/select');
    //修改用户数据
    Route::put('user', 'api/:version.User/update');
    //删除用户数据
    Route::delete('user/:id', 'api/:version.User/delete');

    //获取openid
    Route::get('getOpenid', 'api/:version.User/getOpenid');

    // 音乐
    Route::group('music',function () {
        //获取推荐音乐
        Route::get('recommend/:page/:row', 'api/:version.Music/getRecommendList');
        //查找音乐
        Route::get('search/:query', 'api/:version.Music/searchMusic');
        //获取指定音乐地址
        Route::get(':songId', 'api/:version.Music/getMusic');
    });

    // 获取地理位置
    Route::get('address','api/:version.Site/getAddress');
});