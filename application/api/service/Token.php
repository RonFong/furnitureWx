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
        $userInfo['token'] = self::createToken($userInfo->id);
        return $userInfo;
    }

    /**
     * 此微信用户未注册，则注册，返回用户信息
     * @return $this|null|static
     */
    private static function getUserInfo()
    {
        $userInfo = User::get(['wx_openid' => self::$openid]);
        if (!$userInfo) {
            $userInfo = User::create(['wx_openid' => self::$openid]);
        }
        if (!$userInfo) {
            exception('用户查询或注册失败');
        }
        return $userInfo;
    }

    /**
     * 生成Token并缓存
     * @param $userId
     * @return bool
     */
    private static function createToken($userId)
    {
        $token = md5(str_shuffle(self::$openid));
        $result = Cache::set($token, $userId, 7200);
        if (!$result) {
            exception('token缓存失败');
        }
        return $token;
    }
}