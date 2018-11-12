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
Route::group('api/:version', function () {

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
    Route::group('music', function () {

        //获取音乐库音乐分类
        Route::get('getCategoryList', 'api/:version.Music/getCategoryList');
        //根据分类获取音乐列表
        Route::get('getByCategory', 'api/:version.Music/getByCategory');
        //根据音乐名或艺术家名模糊查找音乐
        Route::get('query', 'api/:version.Music/query');

        /*
         * 因百度音乐接口发生变更，以下接口已不可用
         *
        *获取推荐音乐
        *Route::get('recommend/:page/:row', 'api/:version.Music/getRecommendList');
        *查找音乐
        *Route::get('search/:query', 'api/:version.Music/searchMusic');
        *获取指定音乐地址
        *Route::get('getLink/:id', 'api/:version.Music/getLink');
        */
    });
    //短信
    Route::group('sms', function () {

        //发送短信验证码
        Route::get('getAuthCode', 'api/:version.Sms/getAuthCode');
        //校验短信验证码
        Route::get('checkAuthCode', 'api/:version.Sms/checkAuthCode');
    });
    // 地址信息
    Route::group('site', function () {

        // 获取省市区
        Route::get('region/:parent_id/:level', 'api/:version.Site/getRegion');
        // 获取地理位置
        Route::get('address/:lat/:lng', 'api/:version.Site/getAddress');
        // 获取附近的店
        Route::get('nearby/:lat/:lng', 'api/:version.Site/getNearbyStore');
    });
    //上传图片
    Route::post('image/upload', 'api/:version.Image/saveTmpImg');
    //圈子 文章
    Route::group('article', function () {

        //获取文章列表统一接口
        Route::get('getArticleList', 'api/:version.Article/queryArticleList');

        //获取文章列表统一接口(修改)
        Route::get('queryArticle', 'api/:version.Article/queryArticle');

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
        //缓存文字
        Route::get('setCache', 'api/:version.Article/setCache');
        //获取缓存文字
        Route::get('getCache', 'api/:version.Article/getCache');
        //获取文章部分详情（文字编辑）
        Route::get('getArticleContent', 'api/:version.Article/getArticleContent');
        //保存动态
        Route::post('saveArticleContent', 'api/:version.Article/saveArticleContent');
    });
    Route::group('articleComment', function () {

        //评论文章
        Route::post('comment', 'api/:version.ArticleComment/comment');
        //回复评论
        Route::post('replyComment', 'api/:version.ArticleComment/replyComment');
    });
    // 门店
    Route::group('shop', function () {
        // 入驻商家
        Route::post('create', 'api/:version.Shop/create');
        //获取附近的店
        Route::get('nearby', 'api/:version.Shop/nearby');
        // 门店信息
        Route::get('homePage', 'api/:version.Shop/homePage');
        // 编辑门店注册信息
        Route::get('editRegister','api/:version.Shop/editRegister');
    });
    Route::group('category', function () {

        Route::get('storeList', 'api/:version.StoreClassify/getStoreClassifyList');
    });
    // 用户产品分类
    Route::group('group_classify',function () {
        // 产品分类列表
        Route::get('list','api/:version.Category/getGroupClassifyList');
        // 添加/更新产品分类
        Route::post('add','api/:version.Category/AddGroupClassify');
        // 产品分类排序
        Route::post('sort','api/:version.Category/getSortGroupClassify');
        // 删除产品分类
        Route::post('del','api/:version.Category/delGroupClassify');
        // 二级分类
        Route::get('second','api/:version.Category/getSecondGroupClassify');
    });

    //关注、收藏、点赞
    Route::group('relate', function () {

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
        //用户收藏商家
        Route::post('shopCollect', 'api/:version.Relate/shopCollect');
        //用户收藏厂家
        Route::post('factoryCollect', 'api/:version.Relate/factoryCollect');
        //获取用户的收藏
        Route::get('collectList', 'api/:version.Relate/getCollectList');
        //获取用户的黑名单
        Route::get('blackList', 'api/:version.Relate/getBlackList');
    });
    //工厂
    Route::group('factory', function () {

        Route::post('register', 'api/:version.Factory/register');
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

        //获取首页图文
        Route::get('details', 'api/:version.HomeContent/details');
        //获取首页图文item
        Route::get('getHomeContentItem', 'api/:version.HomeContent/getHomeContentItem');
        //发布首页图文
        Route::post('create', 'api/:version.HomeContent/create');
        //更新首页图文
        Route::put('update', 'api/:version.HomeContent/update');
        //缓存文字
        Route::get('setCache', 'api/:version.HomeContent/setCache');
        //获取缓存文字
        Route::get('getCache', 'api/:version.HomeContent/getCache');
        // 获取商/厂首页图文
        Route::get('getStoreHomeContent','api/:version.HomeContent/getStoreHomeContent');
    });
    //商城
    Route::group('store', function () {

        //获取商城首页商品列表
        Route::get('homeGoodsList', 'api/:version.StoreGoods/getGoodsList');
        // 获取商/厂家基本信息
        Route::get('info','api/:version.Shop/getStoreInfo');
        // 招商代理
        Route::get('attract','api/:version.Shop/getAttract');
    });
    //推广
    Route::group('userProposed', function () {

        //保存推荐关系
        Route::post('proposed', 'api/:version.UserProposed/proposed');
        //获取推荐列表
        Route::get('proposedList', 'api/:version.UserProposed/proposedList');
    });
    //商家自定义商城商品零售价
    Route::group('goodsRetailPrice', function () {

        //设置商城商品全局 零售价计算比例
        Route::post('setGlobalRatio', 'api/:version.GoodsRetailPrice/setGlobalRatio');
        //设置商城商品零售价
        Route::post('setGoodsAmount', 'api/:version.GoodsRetailPrice/setGoodsAmount');
        // 获取零售价计算比例
        Route::get('index','api/:version.GoodsRetailPrice/getGoodsRetailPrice');
    });

    //多媒体文件上传
    Route::post('multimedia/upload', 'api/:version.Multimedia/upload');
});
