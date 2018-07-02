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
use app\lib\sms\SmsApp;
use think\Cache;
use think\Request;

class Sms extends BaseController
{
    protected $smsService;

    function __construct(Request $request = null, SmsApp $smsService)
    {
        parent::__construct($request);
        $this->smsService = $smsService;
        $this->currentValidate = validate('Sms');
    }

    /**
     * 发送短信验证码
     * @param phoneNumber string 手机号
     * @param cacheTime int 验证码有效时间
     * @param  code string  自定义验证码
     * @return array
     */
    public function getAuthCode()
    {
        $this->currentValidate->goCheck('getAuthCode');
        $authCode = $this->smsService->getAuthCode($this->data['phoneNumber']);
        if (!$authCode) {
            $this->result['state'] = 0;
            $this->result['msg'] = '短信发送失败';
        }
        $this->result['data']['auth_code'] = $authCode;
        $this->result['msg'] = '短信发送成功';
        return json($this->result, 200);
    }


    /**
     * 校验短信验证码
     * @param phoneNumber string 手机号
     * @param authCode string 验证码
     * @return array
     */
    public function checkAuthCode()
    {
        $this->currentValidate->goCheck('checkAuthCode');
        $authCode = Cache::get('auth_'.$this->data['phoneNumber']);
        try {
            if (!$authCode) {
               exception('此手机号当前没有可使用的验证码');
            }
            if ($authCode !== $this->data['authCode']) {
               exception('验证码输入错误');
            }
            Cache::rm('auth_'.$this->data['phoneNumber']);
            $this->result['msg'] = '验证通过';
            return json($this->result, 200);
        } catch (\Exception $e) {
            $this->result['state'] = 0;
            $this->result['msg'] = $e->getMessage();
            return json($this->result, 403);
        }
    }
}