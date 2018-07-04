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

namespace app\lib\sms;
use \app\common\model\ErrorLog;
use think\Cache;

/**
 * 腾讯SMS短信服务
 * Class SmsApp
 * @package app\lib\sms
 */
class SmsApp
{
    // 短信应用SDK AppID
    protected $appid = 1400108281; // 1400开头

    // 短信应用SDK AppKey
    protected $appkey = "fe504050bd4bcaed5651ed2f1a093611";

    // 短信模板ID，需要在短信应用中申请
    protected $templateId = [
        'auth_code'    => 148826,     //验证码短信 模板
    ];

    // 签名
    // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
    protected $smsSign = "99家";

    protected $ssender;


    function __construct()
    {
        $this->ssender = new SmsSingleSender($this->appid, $this->appkey);
        $this->errorLog = new ErrorLog();
    }

    /**
     * 单发验证短信
     * @param $phoneNumber string  手机号
     * @param $code string 验证码  不传则随机
     * @param $cacheTime int 有效时间 (分钟)
     * @return bool|string
     */
    public function getAuthCode($phoneNumber, $cacheTime = 30, $code = '')
    {
        $code = $code == '' ? substr(str_shuffle('12345678901234567890123456789'), 0, 6) : $code;
        $msg = "您的验证码是：".$code."，请于".$cacheTime."分钟内填写。如非本人操作，请忽略本短信。";
        $result = $this->ssender->send(0, "86", $phoneNumber, $msg, "", "");
        $res = json_decode($result);
        if ($res->result !== 0) {
            $res->phoneNumber = $phoneNumber;
            $res->code = $code;
            $this->errorLog->saveLog(json_encode($res), 0);
            return false;
        }
        //存入缓存
        Cache::set("auth_".$phoneNumber, $code, $cacheTime * 60);
        return $code;
    }

}