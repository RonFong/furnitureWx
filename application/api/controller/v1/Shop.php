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
        $this->currentModel    = new shopModel();
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
            $this->data['lat'] = sprintf("%.6f",$this->data['lat']);
            $this->data['lng'] = sprintf("%.6f",$this->data['lng']);
            $result = $this->currentModel->save($this->data);
            if (!$result) {
                $this->response->error(Response::UNKNOWN_ERROR);
            }
            $this->result['data'] = $this->currentModel;
            $userInfo = [
                'id'        => user_info('id'),
                'group_id'  => $this->currentModel->id,
                'type'      => 2
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
}