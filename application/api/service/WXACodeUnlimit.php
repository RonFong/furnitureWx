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

namespace app\api\service;


use app\common\validate\BaseValidate;
use think\Cache;
use think\Config;
use think\Request;

/**
 * 生产小程序二维码图片
 * Class WXACodeUnlimit
 * @package app\api\service
 */
class WXACodeUnlimit
{
    /**
     * 请求参数
     * @var int
     */
    private $param = [
        'width'     => 150,                 //图片尺寸
        'page'      => 'pages/storeDetail/storeDetail',  //页面地址
        'scene'     => '1'      //门店ID
    ];


    /**
     * 获取二维码图片
     * @param $page
     * @param $scene
     * @return bool
     */
    public static function create($page, $scene)
    {
        return (new self)->getWXACode($page, $scene);
    }

    /**
     * 获取二维码图片
     * @param $page  string  页面
     * @param $scene string  页面参数
     * @return bool
     */
    public function getWXACode($page, $scene)
    {
        try {
            $this->param['page'] = $page;
            $this->param['scene'] = $scene;
            $base64Code = $this->getBase64Code($this->param);
            $img = $this->filePut($base64Code);
            if (!$img) {
                exception('二维码图片生成失败');
            }
        } catch (\Exception $e) {
            (new BaseValidate())->error($e);
        }
        return $img;
    }

    /**
     * 获取小程序 access_token
     * @return mixed
     */
    private function getAccessToken()
    {
        $appid = Config::get('api.xcx_app_id');
        $secret = Config::get('api.xcx_app_secret');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $accessToken = Cache::get('applet_access');
        if (!$accessToken) {
            $accessToken = curl_get($url);
            Cache::set('applet_access', $accessToken, 7200);
        }
        return (json_decode($accessToken))->access_token;
    }

    /**
     * 获取二维码的 base64 码
     * @param $param string 自定义参数
     * @return string
     */
    private function getBase64Code($param)
    {
        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token={$accessToken}";
        $res = curl_post($url, $param);
        if ($e = json_decode($res)) {
            exception($e->errmsg);
        }
        $base64_image = "data:image/jpeg;base64," . base64_encode($res);
        return $base64_image;
    }

    /**
     * 把返回二维码图片的 Base64码转换成图片并保存
     * @param $base64ImageContent
     * @return bool
     */
    public function filePut($base64ImageContent)
    {
        if (!is_dir(IMAGE_PATH . 'wx_code/')) {
            mkdir(IMAGE_PATH . 'wx_code/', 0777, true);
        }
        $imgName = 'wx_code/' . user_info('id') . time() . '.jpg';
        header('Content-type:text/html;charset=utf-8');
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64ImageContent, $result)) {
            if (file_put_contents(IMAGE_PATH . $imgName, base64_decode(str_replace($result[1], '', $base64ImageContent)))) {
                return Request::instance()->domain() . VIEW_IMAGE_PATH . $imgName;
            }
        }
        return false;
    }
}