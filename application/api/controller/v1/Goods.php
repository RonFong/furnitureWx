<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use think\Request;
use app\api\model\Product as ProductModel;
use app\common\validate\Goods as GoodsValidate;

/**
 * 商城商品
 * Class Goods
 * @package app\api\controller\v1
 */
class Goods extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ProductModel();
        $this->currentValidate = new GoodsValidate();
    }

    /**
     * 获取商城列表
     */
    public function getList()
    {
        $this->currentValidate->goCheck('getList');
        try {
            $this->result['data'] = $this->currentModel
                ->getList($this->data['shop_id'], $this->data['page'] ?? 1, $this->data['row'] ?? 10);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 200);
    }
}