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
     * 获取商城首页商品列表
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