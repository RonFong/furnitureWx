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
}