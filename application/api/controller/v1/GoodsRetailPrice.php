<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/21 
// +----------------------------------------------------------------------


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\lib\enum\Response;
use think\Config;
use think\Request;
use app\api\model\GoodsRetailPrice as GoodsRetailPriceModel;
use app\common\validate\GoodsRetailPrice as GoodsRetailPriceValidate;

/**
 * 商家自定义商城商品零售价
 * Class GoodsRetailPrice
 * @package app\api\controller\v1
 */
class GoodsRetailPrice extends BaseController
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new GoodsRetailPriceModel();
        $this->currentValidate = new GoodsRetailPriceValidate();
    }

    public function getGoodsRetailPrice()
    {
        $config = Config::get('system');
        $this->result['data']['ratio'] = isset($config['price_ratio']) ? $config['price_ratio'] : 1.3;
        return json($this->result, 201);
    }

    /**
     * @api {post} /v1/GoodsRetailPrice/setGlobalRatio  设置商城商品全局 零售价计算比例
     * @apiGroup GoodsRetailPrice
     * @apiParam {float} ratio 零售价计算比例
     *
     * @apiParamExample  {string} 请求参数格式：
     * {"ratio":1.3}
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function setGlobalRatio()
    {
        $this->currentValidate->goCheck('global_ratio');
        try {
            $result = $this->currentModel->setGlobalRatio($this->data);
            if (!$result) {
                $this->response->error(Response::SAVE_TO_FAIL);
            }
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 201);
    }

    /**
     * @api {post} /v1/GoodsRetailPrice/setGoodsAmount  设置商城商品零售价
     * @apiGroup GoodsRetailPrice
     * @apiParam {float} amount 商品零售价
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *  "amount":50000,      //零售价
     *  "goods_id":1          //商品id
     * }
     *
     * @apiSuccessExample {json} 成功时的数据：
     *{
     *  "state": 1,
     *  "msg": "success",
     *  "data": []
     *}
     */
    public function setGoodsAmount()
    {
        $this->currentValidate->goCheck('goods_amount');
        try {
            $result = $this->currentModel->setGoodsAmount($this->data);
            if (!$result) {
                $this->response->error(Response::SAVE_TO_FAIL);
            }
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 201);
    }
}