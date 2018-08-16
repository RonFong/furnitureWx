<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/12 
// +----------------------------------------------------------------------


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\StoreGoods as StoreGoodsValidate;
use app\api\service\StoreGoods as StoreGoodsServer;
use think\Request;

/**
 * 商城商品
 * Class StoreGoods
 * @package app\api\controller\v1
 */
class StoreGoods extends BaseController
{
    protected $currentServer;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentServer = new StoreGoodsServer();
        $this->currentValidate = new StoreGoodsValidate();
    }


    /**
     * @api      {get} /v1/store/homeGoodsList 获取商城首页商品列表
     * @apiGroup Store
     * @apiParam {number} shop_id  当前商家id (商城入口处的商家)
     * @apiParam {number}  [page]  页码
     * @apiParam {number}   [row]  每页条目数
     *
     * @apiParamExample  {string} 请求参数格式：
     * 略
     *
     * @apiSuccessExample {json} 成功时的返回：
    {
        "state": 1,
        "msg": "success",
        "data": [
            {
                "goods_name": "铁王座",
                "goods_no": "Y0123",     //商城商品编号
                "popularity": "340",     //人气值
                "img": "/static/img/tmp/20180816\\\\b8faa0c919ad80eddd6aafc6eb519149_thumb.png",    //缩略图
                "price": "5000.00",      //出厂价， 当前用户为此商城商家时 返回
                "model_no": "SH-0012",   //厂家型号， 当前用户为此商城商家时 返回
                "retail_price": "8200"    //零售价
            },
            {
                "goods_name": "帝王之床",
                "goods_no": "C6542",
                "popularity": "10",
                "img": "/static/img/tmp/20180816\\\\b8faa0c919ad80eddd6aafc6eb519149_thumb.png",
                "price": "6800.00",         //出厂价， 当前用户为此商城商家时 返回
                "model_no": "SH-0013",      //厂家型号， 当前用户为此商城商家时 返回
                "retail_price": 8840
            }
        ]
    }
     *
     * @apiErrorExample {json} 错误返回值：
     * {
     *      "state":0,
     *      "msg":"错误信息",
     *      "data":[]
     * }
     */
    public function getGoodsList()
    {
        $this->currentValidate->goCheck('getGoodsList');
        try {
            $list = $this->currentServer
                ->setBelongToShop($this->data['shop_id'])
                ->setPage($this->page, $this->row)
                ->getHomeList();
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        $this->result['data'] = $list;
        return json($this->result, 200);
    }

}