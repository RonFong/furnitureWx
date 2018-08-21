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
use think\Cache;
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
        // 参数检查暂时跳过
        $this->currentValidate->goCheck('register');
        // 检查手机验证码
        $authCode = Cache::get('auth_'.$this->data['shop_phone']);
        try {
            if (!$authCode) {
                exception('验证码不存在');
            }
            if ($authCode != $this->data['code']) {
                exception('验证码错误');
            }
            Cache::rm('auth_'.$this->data['shop_phone']);
        } catch (\Exception $e) {
            $this->result['state'] = 0;
            $this->result['msg'] = $e->getMessage();
            return json($this->result, 403);
        }

        try {
            $result = $this->currentModel->saveData($this->data);
            if(!$result['success']){
                exception($result['msg']);
            }
        } catch (\Exception $e) {
            return json($this->result, 403);
        }
        $this->result['data'] = $result['data'];
        return json($this->result, 200);
    }

    public function info()
    {
        $admin_id =  user_info('id');
        $userList = $this->currentModel->getShopInfo($admin_id);
        if ($userList) {
            return json($userList);
        }
        $this->response->error(Response::USERS_EMPTY);
    }
}