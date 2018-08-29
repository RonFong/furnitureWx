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
use think\Db;
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
            $this->result['state'] = 0;
            $this->result['msg'] = $e->getMessage();
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

    public function editRegister()
    {
        $user_id = user_info('id');
        $group_id = user_info('group_id');
        $type = user_info('type');
        if($type == 2){
            $detail = $this->currentModel->where('id',$group_id)->where('admin_user',$user_id)->find();
        }elseif ($type == 1){
            $detail = Db::name('factory')->where('id',$group_id)->where('admin_user',$user_id)->find();
        }
        // 数据整理
        $result = [];
        if(!empty($detail)){
            // 查询分类名称
            $classify_name = Db::name('group_classify')
                ->where('id',$detail['category_id'])
                ->column('classify_name');

            $result = [
                'id'    =>  $detail['id'],
                'group_type'    =>  $type,
                'store_name'    =>  isset($detail['shop_name']) ? $detail['shop_name'] : $detail['factory_name'],
                'store_contact'    =>  isset($detail['shop_contact']) ? $detail['shop_contact'] : $detail['factory_contact'],
                'store_phone'    =>  isset($detail['shop_phone']) ? $detail['shop_phone'] : $detail['factory_phone'],
                'store_wx'    =>  isset($detail['shop_wx']) ? $detail['shop_wx'] : $detail['factory_wx'],
                'wx_code'    =>  isset($detail['shop_wx']) ? $detail['shop_wx'] : $detail['factory_wx'],
                'province'  =>  $detail['province'],
                'city'  =>  $detail['city'],
                'district'  =>  $detail['district'],
                'town'  =>  $detail['town'],
                'address'  =>  $detail['address'],
                'shop_img'  =>  isset($detail['shop_img']) ? $detail['shop_img'] : $detail['factory_img'],
                'factory_address'  =>  isset($detail['factory_address']) ? $detail['factory_address'] : '',
                'category_id'   =>  $detail['category_id'],
                'classify_name'   =>  $classify_name[0],
                'category_child_id'   =>  $detail['category_child_id'],
                'license'   =>  $detail['license'],
                'user_name'   =>  $detail['user_name'],
                'phone'   =>  $detail['phone'],
                'wx_account'   =>  $detail['wx_account'],
                'license_code'   =>  $detail['license_code'],
            ];
        }

        $this->result['data'] = $result;
        return json($this->result, 200);
    }
}