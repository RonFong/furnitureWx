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


use app\api\model\User;
use think\Cache;

class Token
{
    /**
     * 微信openid
     * @var
     */
    protected static $openid;


    protected static $userInfo;


    /**
     * 获取 token 和用户信息
     * @param $openid
     * @return Token|null
     */
    public static function getToken($openid)
    {
        if (!$openid) {
            exception('缺少参数：openid');
        }
        self::$openid = $openid;
        $userInfo = self::getUserInfo();
        $userInfo['token'] = self::createToken($userInfo['id']);
        return $userInfo;
    }

    /**
     * 此微信用户未注册，则注册，返回用户信息
     * @return array|null|static
     */
    private static function getUserInfo()
    {
        $userInfo = User::get(['wx_openid' => self::$openid]);
        if (!$userInfo) {
            $saveData = [
                'wx_openid' => self::$openid,
                'avatar'    => config('api.default_avatar'),
                'user_name' => 'wx_' . substr(str_shuffle(self::$openid), 0, 6),
                'group_id'      => 0,
                'gender'        => 0,
                'phone'         => '',
                'wx_account'    => '',
                'type'          => 3,
                'state'         => 1
            ];
            $user = new User();
            print_r($user);
            $result = $user->save($saveData);
//            dump($result);
            die;
            if (!$result) {
                exception('注册失败');
            }
            $saveData['id'] = $user->id;
            return $saveData;
        }
        return $userInfo->toArray();
    }

    /**
     * 生成Token并缓存
     * @param $userId
     * @return bool
     */
    private static function createToken($userId)
    {
        $token = md5(str_shuffle(self::$openid));
        $result = Cache::set($token, $userId, config('api.token_valid_time'));
        if (!$result) {
            exception('token缓存失败');
        }
        return $token;
    }

}