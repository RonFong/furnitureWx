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
        Route::get('getLink/:id', 'api/:version.Music/getLink');
    });

    //短信
    Route::group('sms', function () {
        //发送短信验证码
        Route::get('getAuthCode/:phoneNumber', 'api/:version.Sms/getAuthCode');
        //校验短信验证码
        Route::get('checkAuthCode/:phoneNumber/:authCode', 'api/:version.Sms/checkAuthCode');
    });

    // 地址信息
    Route::group('site',function() {
        // 获取省市区
        Route::get('region/:parent_id/:level','api/:version.Site/getRegion');
        // 获取地理位置
        Route::get('address/:lat/:lng','api/:version.Site/getAddress');
    });

    //保存临时图片
    Route::post('image/temporary', 'api/:version.Image/saveTmpImg');

    //圈子 文章
    Route::group('article', function () {
        //获取文章分类
        Route::get('classify', 'api/:version.Article/getClassify');
        //创建文章
        Route::post('create', 'api/:version.Article/create');
        //更新文章
        Route::put('update', 'api/:version.Article/update');
        //删除文章
        Route::delete('delete', 'api/:version.Article/delete');
    });

    // 门店
    Route::group('shop', function () {
        Route::post('register','api/:version.Shop/register');
    });
});