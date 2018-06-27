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
use app\api\model\User;
use app\lib\enum\Response;
use app\lib\StringFilter;
use think\Request;
use app\api\service\Wechat;

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
    }

    public function register()
    {
        // 请求参数
        $request = $this->data;
        // openid
        if(empty($request['openid'])){
            return $this->jsonReturn(0,'请先授权登录');
        }
        $openid = $request['openid'];
        // 表单信息
        // 门店名称
        if(empty($request['shop_name']) || !is_string($request['shop_name'])){
            return $this->jsonReturn(0,'请填门店名称');
        }
        $shop_name = trim($request['shop_name']);
        // 法人姓名
        if(empty($request['user_name']) || !is_string($request['user_name'])){
            return $this->jsonReturn(0,'请填写法人姓名');
        }
        $user_name = trim($request['user_name']);
        // 法人微信号
        if(empty($request['wx_account']) || !is_string($request['wx_account'])){
            return $this->jsonReturn(0,'请填写法人微信号');
        }
        $wx_account = trim($request['wx_account']);
        // 门头照
        if(empty($request['shop_img']) || !is_string($request['shop_img'])){
            return $this->jsonReturn(0,'请上传门头照');
        }
        $shop_img = trim($request['shop_img']);
        // 手机号
        if(!isset($request['phone']) || !StringFilter::checkPhone($request['phone'])){
            return $this->jsonReturn(0,'手机号码格式错误');
        }
        $phone = StringFilter::controlCharacter($request['phone']);

        // 地址
        if(empty($request['province']) || !is_numeric($request['province'])){
            return $this->jsonReturn(0,'请选择省');
        }
        if(empty($request['city']) || !is_numeric($request['city'])){
            return $this->jsonReturn(0,'请选择市');
        }
        if(empty($request['address']) || !is_string($request['address'])){
            return $this->jsonReturn(0,'请填写详细地址');
        }
        $province = $request['province'];
        $city = $request['city'];
        $address = StringFilter::controlCharacter($request['address']);

        // 根据openid获取用户id
        $user = new User();
        $user_record = $user->checkExistsUser($openid);
        if(!$user_record){
            // TODO 注册成为普通用户还是提示未注册
        }

        $admin_user = $user_record->id;
        $now = time();
        $record = [
            'shop_name' => $shop_name,
            'admin_user' => $admin_user,
            'phone' => $phone,
            'wx_account' => $wx_account,
            'user_name' => $user_name,
            'shop_img' => $shop_img,
            'state' => 1,
            'province' => $province,
            'city' => $city,
            'address' => $address,
            'create_time' => $now,
            'create_by' => $now,
            'update_time' => $now,
            'update_by' => $now,
        ];

        $shopInfo = $this->currentModel->saveData($record);
        if (!$shopInfo) {
            return $this->jsonReturn(0, '提交失败，请重试');
        }
        return $this->jsonReturn(1, '提交成功');
    }
}