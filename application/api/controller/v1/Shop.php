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
use app\api\model\Shop as shopModel;
use app\api\model\ShopCommodity;
use app\api\model\ShopCommodityItem;
use app\common\validate\Shop as shopValidate;
use app\lib\enum\Response;
use app\api\model\User;
use think\Db;
use think\Request;

/**
 * 商家
 * Class Shop
 * @package app\api\controller\v1
 */
class Shop extends BaseController
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new shopModel();
        $this->currentValidate = new shopValidate();
    }

    /**
     * 创建门店
     * @return array
     * @throws \app\lib\exception\BaseException
     */
    public function create()
    {
        $this->currentValidate->goCheck('create');
        try {
            Db::startTrans();
            $this->data['admin_user'] = user_info('id');
            $this->data['lat'] = sprintf("%.6f", $this->data['lat']);
            $this->data['lng'] = sprintf("%.6f", $this->data['lng']);
            $result = $this->currentModel->save($this->data);
            if (!$result) {
                $this->response->error(Response::UNKNOWN_ERROR);
            }
            $this->result['data'] = $this->currentModel;
            $userInfo = [
                'id' => user_info('id'),
                'group_id' => $this->currentModel->id,
                'type' => 2
            ];
            (new User())->save($userInfo);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->response->error($e);
        }
        return json($this->result, 201);
    }

    /**
     * 更新门店信息
     * @return array
     * @throws \app\lib\exception\BaseException
     */
    public function info()
    {
        $this->currentValidate->goCheck('info');
        try {
            if (!empty($this->data['lat']) && !empty($this->data['lng'])) {
                $this->data['lat'] = sprintf("%.6f", $this->data['lat']);
                $this->data['lng'] = sprintf("%.6f", $this->data['lng']);
            }
            $result = $this->currentModel->save($this->data);
            if (!$result) {
                $this->response->error(Response::UNKNOWN_ERROR);
            }
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 201);
    }

    /**
     * 获取附近的商家
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function nearby()
    {
        try {
            $this->result['data'] = $this->currentModel->nearby($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 商家首页信息
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function homePage()
    {
        try {
            if (empty($this->data['shopId']) && user_info('type') != 2) {
                exception('非商家用户，必传 shopId');
            }
            $this->result['data'] = $this->currentModel->homePageData($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }


    /**
     * 发布商品
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function createCommodity()
    {
        (new \app\common\validate\ShopCommodity())->goCheck('createCommodity');
        $this->result['data'] = (new ShopCommodity())->createCommodity($this->data);
        if (!$this->result['data']) {
            $this->response->error(Response::UNKNOWN_ERROR);
        }
        return json($this->result, 201);
    }


    /**
     * 获取商品详情
     * @param int id 商品id
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function commodityDetails()
    {
        try {
            if (empty($this->data['commodity_id'])) {
                exception('commodity_id 不能为空');
            }
            $this->result['data'] = (new ShopCommodity())->commodityDetails($this->data['commodity_id']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);

    }

    /**
     * 删除商品
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function delCommodity()
    {
        Db::startTrans();
        try {
            if (empty($this->data['commodity_id'])) {
                exception('commodity_id 不能为空');
            }
            $delCommodity = (new ShopCommodity())->where('id', $this->data['commodity_id'])->delete();
            $delCommodityItem = (new ShopCommodityItem())->where('commodity_id', $this->data['commodity_id'])->delete();
            if (!$delCommodity || !$delCommodityItem) {
                $this->result['state'] = 0;
                $this->result['msg'] = '删除失败';
            }
            Db::commit();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->response->error($e);
        }
        return json($this->result, 200);

    }
}