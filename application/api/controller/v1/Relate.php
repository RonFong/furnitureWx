<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/16 
// +----------------------------------------------------------------------


namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Relate as RelateServer;
use app\lib\enum\Response;
use app\api\model\User;
use think\Db;
use think\Request;

class Relate extends BaseController
{
    /**
     * 模型列表
     * @var array
     */
    protected $behaviorModel = [
        'articleCollect'    => 'RelationArticleCollect',   //文章收藏
        'articleGreat'      => 'RelationArticleGreat',     //文章点赞
        'commentGreat'      => 'RelationCommentGreat',     //评论点赞
        'goodsCollect'      => 'RelationGoodsCollect',     //用户收藏商城商品
        'userCollect'       => 'RelationUserCollect',      //用户关注用户
        'factoryBlacklist'  => 'RelationFactoryBlacklist', //厂家 拉黑 商家
        'shopBlacklist'     => 'RelationShopBlacklist',    //商家 拉黑 厂家
        'goodsBlacklist'    => 'RelationGoodsBlacklist',   //商家 拉黑 商品
        'shopCollect'       => 'RelationShopCollect',      //用户收藏商家
        'factoryCollect'    => 'RelationFactoryCollect',   //商家 收藏厂家
    ];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentValidate = validate('Relate');
    }

    /**
     * @param $state string 结果
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    protected function return($state)
    {
        if ($state == false) {
            $this->response->error(Response::RELATE_ERROR);
        }
        return json($this->result, 200);
    }

    /**
     * @api {post} /v1/relate/articleCollect  用户收藏文章
     * @apiGroup Relate
     * @apiParam {number} article_id 文章ID
     * @apiParam {string} type inc(收藏) 或 dec(取消收藏)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "article_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function articleCollect()
    {
        $this->currentValidate->goCheck('articleCollect');
        return $this->return((new RelateServer($this->behaviorModel['articleCollect']))->save($this->data));
    }

    /**
     * @api {post} /v1/relate/shopCollect  用户收藏商家
     * @apiGroup Relate
     * @apiParam {number} shop_id 文章ID
     * @apiParam {string} type inc(收藏) 或 dec(取消收藏)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "shop_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function shopCollect()
    {
        $this->currentValidate->goCheck('shopCollect');
        return $this->return((new RelateServer($this->behaviorModel['shopCollect']))->save($this->data));
    }

    /**
     * @api {post} /v1/relate/factoryCollect  用户收藏厂家
     * @apiGroup Relate
     * @apiParam {number} factory_id 文章ID
     * @apiParam {string} type inc(收藏) 或 dec(取消收藏)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "factory_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function factoryCollect()
    {
        $this->currentValidate->goCheck('factoryCollect');
        return $this->return((new RelateServer($this->behaviorModel['factoryCollect']))->save($this->data));
    }


    /**
     * @api {post} /v1/relate/articleGreat  用户点赞文章
     * @apiGroup Relate
     * @apiParam {number} article_id 文章ID
     * @apiParam {string} type inc(点赞) 或 dec(取消点赞)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "article_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function articleGreat()
    {
        $this->currentValidate->goCheck('articleGreat');
        return $this->return((new RelateServer($this->behaviorModel['articleGreat']))->save($this->data));
    }


    /**
     * @api {post} /v1/relate/commentGreat  用户点赞评论
     * @apiGroup Relate
     * @apiParam {number} comment_id 评论ID
     * @apiParam {string} type inc(点赞) 或 dec(取消点赞)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "comment_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function commentGreat()
    {
        $this->currentValidate->goCheck('commentGreat');
        return $this->return((new RelateServer($this->behaviorModel['commentGreat']))->save($this->data));
    }


    /**
     * @api {post} /v1/relate/goodsCollect  用户收藏商城商品
     * @apiGroup Relate
     * @apiParam {number} goods_id 商品ID
     * @apiParam {string} type inc(关注) 或 dec(取消关注)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "goods_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function goodsCollect()
    {
        $this->currentValidate->goCheck('goodsCollect');
        return $this->return((new RelateServer($this->behaviorModel['goodsCollect']))->save($this->data));
    }

    /**
     * @api {post} /v1/relate/shopCollect  用户关注用户
     * @apiGroup Relate
     * @apiParam {number} user_id 被关注的用户id
     * @apiParam {string} type inc(关注) 或 dec(取消关注)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "user_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function userCollect()
    {
        $this->currentValidate->goCheck('userCollect');
        return $this->return((new RelateServer($this->behaviorModel['userCollect']))->save($this->data));
    }

    /**
     * @api {post} /v1/relate/factoryBlacklist  厂家拉黑商家
     * @apiGroup Relate
     * @apiParam {number} shop_id 评论ID
     * @apiParam {string} type inc(拉黑) 或 dec(取消拉黑)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "shop_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function factoryBlacklist()
    {
        $this->currentValidate->goCheck('factoryBlacklist');
        $this->data['factory_id'] = (new User())->where(['id' => user_info('id'), 'type' => 1])->value('group_id');
        if (!$this->data['factory_id']) {
            $this->response->error(Response::IS_NOT_FACTORY);
        }
        return $this->return((new RelateServer($this->behaviorModel['factoryBlacklist']))->save($this->data));
    }

    /**
     * @api {post} /v1/relate/factoryBlacklist  商家 拉黑 厂家
     * @apiGroup Relate
     * @apiParam {number} factory_id 评论ID
     * @apiParam {string} type inc(拉黑) 或 dec(取消拉黑)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "factory_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function shopBlacklist()
    {
        $this->currentValidate->goCheck('shopBlacklist');
        $this->data['shop_id'] = (new User())->where(['id' => user_info('id'), 'type' => 2])->value('group_id');
        if (!$this->data['shop_id']) {
            $this->response->error(Response::IS_NOT_SHOP);
        }
        return $this->return((new RelateServer($this->behaviorModel['shopBlacklist']))->save($this->data));
    }

    /**
     * @api {post} /v1/relate/factoryBlacklist  商家 拉黑 商城商品
     * @apiGroup Relate
     * @apiParam {number} goods_id 评论ID
     * @apiParam {string} type inc(拉黑) 或 dec(取消拉黑)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "goods_id":1,
     *      "type":"inc"
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function goodsBlacklist()
    {
        $this->currentValidate->goCheck('goodsBlacklist');
        $this->data['shop_id'] = (new User())->where(['id' => user_info('id'), 'type' => 2])->value('group_id');
        if (!$this->data['shop_id']) {
            $this->response->error(Response::IS_NOT_SHOP);
        }
        return $this->return((new RelateServer($this->behaviorModel['goodsBlacklist']))->save($this->data));
    }


    /**
     * @api {get} /v1/relate/collectList  获取用户收藏列表
     * @apiGroup Relate
     * @apiParam {string} category  空或不传 则默认获取全部，goods - 厂家产品 ；shop 商家 ； factory 厂家
     * @apiParam {number} page
     * @apiParam {number} row
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "category":"goods",
     *      "page":1,
     *      "row":10
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "factory": {
     *      "name": "厂家",
     *      "list": []      //category = factory 时有值
     *  },
     *  "shop": {
     *      "name": "经销商",
     *      "list": []      //category = shop 时有值
     *  },
     *  "goods": {
     *      "name": "商品",
     *      "list": []      //category = goods 时有值
     *  },
     *  "default": [        //category 为空或未传 时有值
     *      {
     *          "id": 8,
     *          "factory_name": "双虎家居",
     *          "factory_img": "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531989310611&di=899df6ccda3f15e7875f30c017cc5cf1&imgtype=0&src=http%3A%2F%2Fwww.alrui.com%2Fuploads%2Fallimg%2F180601%2F162I64395_0.jpg",
     *          "state": 1,             // 禁用状态， 为 0 时， 提示该 厂家/商家/商品 已 冻结/下架
     *          "deleted": "",          // 删除状态， 为 0 时则未删除，  非 0 时 （时间戳）则提示  该 厂家/商家/商品 已 不存在/删除
     *          "create_time": "2018-08-21"         //收藏时间
     *      },
     *      {
     *          "id": 1,
     *          "goods_name": "铁王座",
     *          "state": 1,
     *          "deleted": "0",
     *          "shop_img": "/static/img/tmp/20180816\\\\b8faa0c919ad80eddd6aafc6eb519149.png",
     *          "create_time": "2018-07-17"
     *      },
     *      {
     *          "id": 7,
     *          "shop_name": "三有家具城",
     *          "shop_img": "/static/img/tmp/20180805\\209325e33c678d22c08c9a5e6715a1a3.jpg",
     *          "state": 1,
     *          "deleted": "0",
     *          "create_time": "2018-07-17"
     *      }
     *  ]
     *}
     */
    public function getCollectList()
    {
        try {
            $this->result['data'] = (new RelateServer())->getCollectList($this->data, $this->page, $this->row);
        } catch (\Exception $e) {
            $this->response->error($e);
        }

        return json($this->result, 200);
    }


    /**
     * @api {get} /v1/relate/blackList  获取当前用户的黑名单
     * @apiGroup Relate
     * @apiParam {string} category  空或不传 则默认获取全部，goods - 厂家产品 ；shop 商家 ； factory 厂家
     * @apiParam {number} page
     * @apiParam {number} row
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "category":"goods",
     *      "page":1,
     *      "row":10
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "factory": {                            //当前用户为商家时存在
     *      "name": "厂家",
     *      "list": []      //category = factory 时有值
     *  },
     *  "shop": {                                //当前用户为厂家时存在
     *      "name": "经销商",
     *      "list": []      //category = shop 时有值
     *  },
     *  "goods": {                              //当前用户为商家时存在
     *      "name": "商品",
     *      "list": []
     *  },
     *  "default": [                            //category 为空或未传 时有值
     *      {
     *          "id": 8,
     *          "factory_name": "双虎家居",
     *          "factory_img": "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531989310611&di=899df6ccda3f15e7875f30c017cc5cf1&imgtype=0&src=http%3A%2F%2Fwww.alrui.com%2Fuploads%2Fallimg%2F180601%2F162I64395_0.jpg",
     *          "state": 1,             // 禁用状态， 为 0 时， 提示该 厂家/商家/商品 已 冻结/下架
     *          "deleted": "",          // 删除状态， 为 0 时则未删除，  非 0 时 （时间戳）则提示  该 厂家/商家/商品 已 不存在/删除
     *          "create_time": "2018-08-21"         //收藏时间
     *      },
     *      {
     *          "id": 1,
     *          "goods_name": "铁王座",
     *          "state": 1,
     *          "deleted": "0",
     *          "shop_img": "/static/img/tmp/20180816\\\\b8faa0c919ad80eddd6aafc6eb519149.png",
     *          "create_time": "2018-07-17"
     *      },
     *      {
     *          "id": 7,
     *          "shop_name": "三有家具城",
     *          "shop_img": "/static/img/tmp/20180805\\209325e33c678d22c08c9a5e6715a1a3.jpg",
     *          "state": 1,
     *          "deleted": "0",
     *          "create_time": "2018-07-17"
     *      }
     *  ]
     *}
     */
    public function getBlackList()
    {
        try {
            $result = (new RelateServer())->getBlackList($this->data, $this->page, $this->row);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($result, 200);
    }

}