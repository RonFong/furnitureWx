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
        'factoryCollect'    => 'RelationFactoryCollect',   //商家关注厂家
        'goodsCollect'      => 'RelationGoodsCollect',     //用户收藏商城商品
        'shopCollect'       => 'RelationShopCollect'       //用户收藏商家
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
     * @api {post} /v1/relate/factoryCollect  商家关注厂家
     * @apiGroup Relate
     * @apiParam {number} shop_id 商家ID
     * @apiParam {number} factory_id 厂家ID
     * @apiParam {string} type inc(关注) 或 dec(取消关注)
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "shop_id":1,
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
     * @api {post} /v1/relate/shopCollect  用户收藏商家
     * @apiGroup Relate
     * @apiParam {number} shop_id 评论ID
     * @apiParam {string} type inc(关注) 或 dec(取消关注)
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
}