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


use app\api\service\Wechat;
use app\api\service\Token as TokenServer;
use think\Request;

class Token
{
    /**
     * 用户信息  （新用户，则注册）
     * @return mixed
     */
    public function getToken()
    {
        try {
            if (!Request::instance()->has('code','get')) {
                exception('code 参数不能为空');
            }
            $code = Request::instance()->param('code');
            $openid = (new Wechat())->getOpenid(['code' => $code]);
            if (!$openid) {
                exception('获取用户openid失败');
            }
            $userInfo = TokenServer::getToken($openid);
        } catch (\Exception $e) {
            return json(['state' => 0, 'msg' => $e->getMessage()], 400);
        }
        return json($userInfo, 200);
    }
}