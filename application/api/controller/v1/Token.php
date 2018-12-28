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


use app\api\model\User;
use app\api\service\Wechat;
use app\api\service\Token as TokenServer;
use app\common\validate\BaseValidate;
use think\Request;
use app\api\validate\Token as TokenValidate;

class Token
{

    /**
     * 获取userToken
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function getToken()
    {
        try {
            (new TokenValidate())->goCheck('getToken');

            $param = Request::instance()->param();
            $wxInfo = (new Wechat())->getOpenid($param['code']);
            if (!$wxInfo['state']) {
                exception($wxInfo['msg']);
            }
            $data = TokenServer::getToken($wxInfo['openid'], json_decode($param['userInfo'], true) ?? $param['userInfo']);
            $token = $data['token'];
            unset($data['token']);
            $result = [
                'state'     => 1,
                'msg'       => 'success',
                'data'      => [
                    'token'     => $token,
                    'user_info' => $data
                ]
            ];
            return json($result, 200);
        } catch (\Exception $e) {
            (new BaseValidate())->error($e);
        }
    }

    /**
     * （测试用）通过用户ID直接获取 token
     */
    public function getTestToken()
    {
        try {
            $user = User::get(input('id'));
            $data = TokenServer::getToken($user->wx_openid, Request::instance()->param());
            $token = $data['token'];
            unset($data['token']);
            $result = [
                'state' => 1,
                'msg' => 'success',
                'data' => [
                    'token' => $token,
                    'user_info' => $data
                ]
            ];
        } catch (\Exception $e) {
            return json(['state' => 0, 'msg' => $e->getMessage()], 400);
        }
        return json($result, 200);
    }
}
