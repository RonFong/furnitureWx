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
use app\lib\enum\Response;
use think\Request;

class Shop extends BaseController
{
    /**
     * 参数校验统一入口方法
     * @param string $scene     场景
     * @param array $rules      规则
     * Shop constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null) {

        parent::__construct($request);
        $this->currentModel    = new shopModel();
        $this->currentValidate = validate('shop');
    }

    public function register()
    {
        $this->currentValidate->goCheck('register');
        if(isset($this->data['contact'])){
            $this->data['shop_contact'] = $this->data['contact'];
            unset($this->data['contact']);
        }
        if(isset($this->data['store_phone'])){
            $this->data['shop_phone'] = $this->data['store_phone'];
            unset($this->data['store_phone']);
        }
        if(isset($this->data['store_wx'])){
            $this->data['shop_wx'] = $this->data['store_wx'];
            unset($this->data['store_wx']);
        }
        $shop = $this->currentModel->saveData($this->data);
        if ($shop) {
            $this->result['data'] = $this->currentModel->where('id',$shop->id)->find()->toArray();
            return json($this->result, 201);
        }
        $this->response->error(Response::SHOP_REGISTER_ERROR);
    }

    public function info()
    {
        $this->currentValidate->goCheck('info');
        $this->data['admin_user'] ??
        $userList = $this->currentModel->selectShop(, $this->page, $this->row);
        if ($userList) {
            return json($userList, 202);
        }
        $this->response->error(Response::USERS_EMPTY);
    }
}