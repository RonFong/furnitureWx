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
    //修改昵称
    Route::put('user/changeName', 'api/:version.User/changeName');
    //修改头像
    Route::put('user/changeAvatar', 'api/:version.User/changeAvatar');
    //用户名片信息
    Route::get('user/info', 'api/:version.User/info');
    //保存用户位置信息
    Route::post('user/location', 'api/:version.User/location');
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
    });
    //短信
    Route::group('sms', function () {
        //发送短信验证码
        Route::get('getAuthCode', 'api/:version.Sms/getAuthCode');
        //校验短信验证码
        Route::get('checkAuthCode', 'api/:version.Sms/checkAuthCode');
    });
    //圈子 文章
    Route::group('article', function () {
        //获取文章分类
        Route::get('classify', 'api/:version.Article/getClassify');
        //创建文章
        Route::post('create', 'api/:version.Article/create');
        //更新文章
        Route::put('draft', 'api/:version.Article/draft');
        //删除文章
        Route::delete('delete', 'api/:version.Article/delete');
        //更新文章
        Route::put('update', 'api/:version.Article/update');
        //同城圈首页文章列表
        Route::get('list', 'api/:version.Article/list');
        //推荐文章列表
        Route::get('recommend', 'api/:version.Article/recommend');
        //按用户获取文章列表
        Route::get('listGroupByUser', 'api/:version.Article/listGroupByUser');
        //增加文章的一个分享数
        Route::put('share', 'api/:version.Article/share');
        //获取文章详情
        Route::get('details', 'api/:version.Article/details');
        //我收藏的文章列表
        Route::get('articleCollectList', 'api/:version.Article/articleCollectList');
        //我关注的用户
        Route::get('myCollect', 'api/:version.Article/myCollect');
        //我的粉丝
        Route::get('collectMe', 'api/:version.Article/collectMe');

    });
    Route::group('articleComment', function () {
        //评论文章
        Route::post('publish', 'api/:version.ArticleComment/comment');
        //回复评论
        Route::post('reply', 'api/:version.ArticleComment/reply');
        //获取文章更多评论
        Route::get('more', 'api/:version.ArticleComment/getMore');
        //获取评论的所有回复
        Route::get('moreReply', 'api/:version.ArticleComment/moreCommentReply');
    });

    // 商家门店
    Route::group('shop', function () {
        // 入驻商家
        Route::post('create', 'api/:version.Shop/create');
        // 获取门店首页信息
        Route::get('homePage', 'api/:version.Shop/homePage');
        // 获取门店信息 & 首页图文
        Route::get('info', 'api/:version.Shop/shopInfo');
        // 修改门店信息
        Route::put('info', 'api/:version.Shop/updateInfo');
        // 编辑门店注册信息
        Route::get('editRegister','api/:version.Shop/editRegister');
        // 发布商品
        Route::post('createCommodity','api/:version.Shop/createCommodity');
        // 查看商品详情
        Route::get('commodityDetails','api/:version.Shop/commodityDetails');
        // 删除商品
        Route::delete('delCommodity','api/:version.Shop/delCommodity');
    });

    //获取附近的店
    Route::get('group/nearby', 'api/:version.Shop/nearby');
    Route::get('shop/nearby', 'api/:version.Shop/nearby');

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
    //厂家
    Route::group('factory', function () {
        //创建厂家门店
        Route::post('create', 'api/:version.Factory/create');
        //修改厂家信息
        Route::put('update', 'api/:version.Factory/update');
        //获取厂家信息
        Route::get('info', 'api/:version.Factory/info');
        //新建分类
        Route::post('classify', 'api/:version.FactoryProductClassify/create');
        //修改分类名或排序
        Route::put('classify', 'api/:version.FactoryProductClassify/edit');
        //首页信息
        Route::get('homePage', 'api/:version.Factory/homePage');
        //获取分类
        Route::get('classifyList', 'api/:version.FactoryProductClassify/getList');
        //添加简介
        Route::post('intro', 'api/:version.Factory/createIntro');
        //修改简介
        Route::put('intro', 'api/:version.Factory/updateIntro');
        //获取简介详情
        Route::get('intro', 'api/:version.Factory/introInfo');
        //获取联系信息
        Route::get('contactInfo', 'api/:version.Factory/contactInfo');
    });

    //厂家产品
    Route::group('product', function () {
        //发布产品
        Route::post('create', 'api/:version.product/create');
        //修改产品信息
        Route::put('update', 'api/:version.product/update');
        //产品信息
        Route::get('info', 'api/:version.product/info');
        //根据分类获取产品列表
        Route::get('getListByClassify', 'api/:version.product/getListByClassify');
        //产品零售价计算比例
        Route::get('retailPriceRatio', 'api/:version.product/retailPriceRatio');
        //产品编辑页所需数据   默认品牌、分类、零售价计算比例
        Route::get('attr', 'api/:version.product/attr');
        //删除产品分类
        Route::delete('classify', 'api/:version.FactoryProductClassify/delClassify');
        //删除产品
        Route::delete('/', 'api/:version.product/delProduct');
        //产品移动到其他分类
        Route::put('changeClassify', 'api/:version.product/changeClassify');
        //产品移动到其他分类
        Route::put('changeClassify', 'api/:version.product/changeClassify');
        //更改产品排序
        Route::put('sort', 'api/:version.product/sort');
        //更改产品上下架状态
        Route::put('shelves', 'api/:version.product/shelvesStatus');
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
    Route::group('user', function () {
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

    //上传音频
    Route::post('multimedia/uploadAudio', 'api/:version.Multimedia/uploadAudio');
    //上传视频
    Route::post('multimedia/uploadVideo', 'api/:version.Multimedia/uploadVideo');
    //删除文件
    Route::post('multimedia/delete', 'api/:version.Multimedia/delete');
    //上传图片到OSS
    Route::post('image/uploadToOss', 'api/:version.Multimedia/uploadImg');
    //上传图片到服务器
    Route::post('image/upload', 'api/:version.Image/saveTmpImg');

    //获取当前用户与某用户的聊天消息
    Route::get('message/logWithUser', 'api/:version.Message/logWithUser');

    //商城
    Route::group('store', function () {
        //首页获取商品列表
        Route::get('goods/list', 'api/:version.goods/getList');
    });

});

