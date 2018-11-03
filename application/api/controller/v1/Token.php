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

class Token
{
    /**
     * @api {get} /v1/getToken 获取userToken
     * @apiGroup Token
     * @apiParam {string} code 微信用户的code
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "code":"*********"
     * }
     *
     * @apiSuccessExample {json} 成功时的返回：
     *  {
     *      "state": 1,
     *      "msg": "success",
     *      "data": {
     *      "token": "421ba5cb275fa6ee871d8288cffdbd17",
     *      "user_info": {
     *              "id": 16,
     *              "user_name": "test",
     *              "group_id": 0,
     *              "avatar": "",
     *              "gender": 0,
     *              "phone": "1817074852",
     *              "wx_account": "eeeFtyrty",
     *              "type": 3,
     *              "state": 0,
     *              "create_time": "2018-06-15 04:15:23"
     *          }
     *      }
     *  }
     *
     * @apiErrorExample {json} 错误返回值：
     * {
     *      "state":0,
     *      "msg":"错误信息",
     *      "data":[]
     * }
     */
    public function getToken()
    {
        try {
            if (!Request::instance()->has('code','get')) {
                exception('code 参数不能为空');
            }
            $code = Request::instance()->param('code');
            $openid = (new Wechat())->getOpenid(['code' => $code]);
            dump($openid);
            die;
            if (!$openid) {
                exception('获取用户openid失败');
            }
            $data = TokenServer::getToken($openid);
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
        } catch (\Exception $e) {
            (new BaseValidate())->error($e);
            return json(['state' => 0, 'msg' => $e->getMessage()], 400);
        }
        return json($result, 200);
    }

    /**
     * （测试用）通过用户ID直接获取 token
     */
    public function getTestToken()
    {
        try {
            $user = User::get(input('id'));
            $data = TokenServer::getToken($user->wx_openid);
            $token = $data->token;
            unset($data->token);
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
