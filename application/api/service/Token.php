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
use think\Db;

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
     * @param $userInfo
     * @return Token|array|null
     */
    public static function getToken($openid, $userInfo)
    {
        if (!$openid) {
            exception('缺少参数：openid');
        }
        self::$openid = $openid;
        $userInfo = self::getUserInfo($userInfo);
        $userInfo['token'] = self::createToken($userInfo['id']);
        return $userInfo;
    }

    /**
     * 此微信用户未注册，则注册，返回用户信息
     * @param $userInfo array 用户的微信信息
     * @return array|null|static
     */
    private static function getUserInfo($userInfo)
    {
        $userInfo = User::get(['wx_openid' => self::$openid]);
        if (!$userInfo) {

            $userName = $userInfo['nickName'] ?? 'wx_' . substr(str_shuffle(self::$openid), 0, 6);
            $city = $userInfo['city'] ? Db::table('district')->where('pinyin', strtolower($userInfo['city']))->value('name') : '';
            $province = $userInfo['province'] ? Db::table('district')->where('pinyin', strtolower($userInfo['province']))->value('name') : '';

            $saveData = [
                'wx_openid'     => self::$openid,
                'avatar'        => $userInfo['avatarUrl'] ?? config('api.default_avatar'),
                'user_name'     => $userName,
                'group_id'      => 0,
                'gender'        => $userInfo['gender'] ?? 0,
                'city'          => $city,
                'province'      => $province,
                'phone'         => '',
                'wx_account'    => '',
                'type'          => 3,
                'state'         => 1
            ];
            $id = Db::table('user')->insertGetId($saveData);
            if (!$id) {
                exception('注册失败');
            }
            $saveData['id'] = $id;
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