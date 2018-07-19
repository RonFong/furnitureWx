<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/14
// +----------------------------------------------------------------------
namespace app\api\service;

use think\Cache;

class Wechat {

    public $appid;
    public $token;
    public $appSecret;

    public function __construct() {

        $this->appid     = 'wx195a5e8ed1a55ead';
        $this->appSecret = 'd0a065f66e34734712f8b4310691b5c3';

        $this->token     = '';

    }

    /**
     * 根据code获取openid和session_key
     * @param $data
     * @return mixed
     */
    public function getOpenid($data) {

        $jsCode = $data['code'];
        $url         = $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$this->appid&secret=$this->appSecret&js_code=$jsCode&grant_type=authorization_code";
        $info = json_decode(curl_get($url));

        //Cache::set('session_key:'.$info->openid,$info->session_key);
        return $info->openid;
    }

    /**
     * 获取接口调用凭证accessToken
     * @return bool|string
     */
    public function getWxAccessToken() {

        $url         = $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->appSecret";
        $res = json_decode(curl_get($url));

        return $res->access_token;
    }

}
