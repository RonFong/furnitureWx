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

    //获取token
    Route::get('getToken', 'api/:version.Token/getToken');
    //（测试用）通过用户ID直接获取 token
    Route::get('getTestToken', 'api/:version.Token/getTestToken');

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
        //同城圈首页文章列表
        Route::get('localList', 'api/:version.Article/localArticleList');
        //增加文章的一个分享数
        Route::put('share', 'api/:version.Article/share');
        //获取文章详情
        Route::get('details', 'api/:version.Article/details');
        //获取用户本人的文章列表
        Route::get('ownList', 'api/:version.Article/getOwnArticleList');
        //根据用户ID获取文章列表
        Route::get('listByUserId', 'api/:version.Article/getArticleListByUserId');
        //按分类获取文章列表
        Route::get('listByClassify', 'api/:version.Article/getListByClassify');
        //我的收藏
        Route::get('myCollectArticle', 'api/:version.Article/myCollectArticle');
        //我关注的用户
        Route::get('myCollect', 'api/:version.Article/myCollect');
        //我的粉丝
        Route::get('collectMe', 'api/:version.Article/collectMe');
        //获取文章更多评论
        Route::get('moreComment', 'api/:version.Article/getMoreComment');
    });

    Route::group('articleComment', function () {
        //评论文章
        Route::post('comment', 'api/:version.ArticleComment/comment');
        //回复评论
        Route::post('replyComment', 'api/:version.ArticleComment/replyComment');
    });

    // 门店
    Route::group('shop', function () {
        Route::post('register','api/:version.Shop/register');
    });

    Route::group('category',function (){
        Route::get('storeList','api/:version.StoreClassify/getStoreClassifyList');
    });

    //关注、收藏、点赞
    Route::group('relate', function() {
        //用户收藏文章
        Route::post('articleCollect', 'api/:version.Relate/articleCollect');
        //用户点赞文章
        Route::post('articleGreat', 'api/:version.Relate/articleGreat');
        //评论点赞
        Route::post('commentGreat', 'api/:version.Relate/commentGreat');
        //用户收藏商城商品
        Route::post('goodsCollect', 'api/:version.Relate/goodsCollect');
        //用户关注用户
        Route::post('userCollect', 'api/:version.Relate/userCollect');
        //厂家 拉黑 商家
        Route::post('factoryBlacklist', 'api/:version.Relate/factoryBlacklist');
        //商家 拉黑 厂家
        Route::post('shopBlacklist', 'api/:version.Relate/shopBlacklist');
        //商家 拉黑 商城商品
        Route::post('goodsBlacklist', 'api/:version.Relate/goodsBlacklist');
    });

    //工厂
    Route::group('factory', function () {
        Route::post('register','api/:version.Factory/register');
        //获取所有工厂
        Route::get('factoryList', 'api/:version.Factory/getFactoryList');
        //获取工厂产品
        Route::get('factoryProduct', 'api/:version.Factory/getFactoryProduct');
        //获取工厂产品详情
        Route::get('factoryProductDetail/:product_id', 'api/:version.Factory/factoryProductDetail');
        //获取工厂详情
        Route::get('getFactoryInfo', 'api/:version.Factory/getFactoryInfo');
        //编辑工厂信息
        Route::post('editFactoryInfo', 'api/:version.Factory/editFactoryInfo');
    });

    //首页图文
    Route::group('homeContent', function () {
        //发布首页图文
        Route::get('getHomeContentItem', 'api/:version.HomeContent/getHomeContentItem');
        //发布首页图文
        Route::post('addHomeContent', 'api/:version.HomeContent/addHomeContent');
        //保存首页图文
        Route::post('saveHomeContent', 'api/:version.HomeContent/saveHomeContent');
    });

    //商城
    Route::group('store', function () {
        //获取商城首页商品列表
        Route::get('homeGoodsList', 'api/:version.StoreGoods/getGoodsList');
    });
});
