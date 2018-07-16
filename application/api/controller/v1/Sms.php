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
     * @api {get} /v1/sms/getAuthCode/:phoneNumber 发送短信验证码
     * @apiGroup SMS
     * @apiParam {string} phoneNumber 手机号
     *
     * @apiParamExample  {string} 请求参数格式：
     * 见接口地址
     *
     * @apiSuccessExample {json} 成功时的返回：
     * {
     *      "state":1,
     *      "msg":"短信发送成功",
     *      "data":{
     *          "auth_code":"308299"    //验证码，接收后可前端验证用户输入，也可通过请求验证接口校验
     *      }
     * }
     *
     * @apiErrorExample {json} 错误返回值：
     * {
     *      "state":0,
     *      "msg":"错误信息",
     *      "data":[]
     * }
     */
    public function getAuthCode()
    {
        $this->currentValidate->goCheck('getAuthCode');
        try {
            $cacheCode = Cache::get('send_'.$this->data['phoneNumber']);
            if ($cacheCode) {
                exception('短信发送频率过高，请稍后再试');
            }
            $authCode = $this->smsService->getAuthCode($this->data['phoneNumber']);
            if (!$authCode) {
                exception('短信发送失败');
            }
            Cache::set("send_".$this->data['phoneNumber'], $authCode, 60);
            $this->result['data']['auth_code'] = $authCode;
            $this->result['msg'] = '短信发送成功';
        } catch (\Exception $e) {
            $code = 403;
            $this->result['state'] = 0;
            $this->result['msg'] = $e->getMessage();
        }
        return json($this->result, $code ?? 200);
    }


    /**
     * @api {get} /v1/sms/checkAuthCode/:phoneNumber/:authCode 校验短信验证码
     * @apiGroup SMS
     * @apiParam {string} phoneNumber 手机号
     * @apiParam {string} authCode 验证码
     *
     * @apiParamExample  {string} 请求参数格式：
     * 见接口地址
     *
     * @apiSuccessExample {json} 成功时的返回：
     * {
     *      "state":1,
     *      "msg":"验证通过",
     *      "data":[]
     * }
     *
     * @apiErrorExample {json} 错误返回值：
     * {
     *      "state":0,
     *      "msg":"错误信息",
     *      "data":[]
     * }
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
               exception('验证码错误');
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