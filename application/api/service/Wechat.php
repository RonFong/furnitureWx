<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/14
// +----------------------------------------------------------------------
namespace app\api\service;

class Wechat {

    public $appid;
    public $token;
    public $appSecret;

    public function __construct() {

        $this->appid     = 'wx195a5e8ed1a55ead';
        $this->appSecret = '7a91a512262ec4a9dea5a507612e48a0';
        $this->token     = '';

    }

    /**
     * 根据code获取openid和seesion_key
     * @return bool|string
     */
    public function getOpenid($data) {
        $jsCode = $data['jsCode'];
        $url         = $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$this->appid&secret=$this->appSecret&js_code=$jsCode&grant_type=authorization_code";
        $res = json_decode($this->httpGet($url));

        return $res->openid;
    }

    /**
     * 获取接口调用凭证accessToken
     * @return bool|string
     */
    public function getWxAccessToken() {

        $url         = $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->appSecret";
        $res = $this->httpGet($url);

        return $res->access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);

        curl_close($curl);

        return $res;
    }

}
