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

namespace app\api\behavior;


use think\Request;
use think\Cache;
use think\Session;
use app\api\model\User;


class CheckToken
{
    public function run()
    {
        try {
            if ($token = Request::instance()->header('userToken')) {
                if (!$userId = Cache::get($token))
                    //errorCode 值为  10000 时， 前端将重新请求 getToken 接口，此错误码不可更改和重用
                    die(json_encode(['state' => 0, 'errorCode' => 10000, 'msg' => '无效的userToken']));
                if (!$userInfo = User::get($userId))
                    exception('用户数据异常');
                if ($userInfo->state == 0)
                    exception('账号被冻结');
                Session::set('user_info', $userInfo->toArray());
            } else {
                exception('头文件中userToken参数不能为空');
            }
        } catch (\Exception $e) {
            die(json_encode(['state' => 0, 'errorCode' => 1003, 'msg' => $e->getMessage()]));
        }
    }
}