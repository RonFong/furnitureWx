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
use app\common\model\UserLocation;
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
     * @return Token|null
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
     * @param $wxUserInfo
     * @return array
     */
    private static function getUserInfo($wxUserInfo)
    {
        $userInfo = User::get(['wx_openid' => self::$openid]);
        $locationData = [
            'lng'           => sprintf("%.6f", $wxUserInfo['lng']),
            'lat'           => sprintf("%.6f", $wxUserInfo['lat']),
            'update_time'   => time()
        ];
        if (!$userInfo) {
            $saveData = [
                'wx_openid'     => self::$openid,
                'avatar'        => $wxUserInfo['avatarUrl'],
                'user_name'     => $wxUserInfo['nickName'],
                'group_id'      => 0,
                'gender'        => $wxUserInfo['gender'],
                'province'      => $wxUserInfo['province'],
                'city'          => $wxUserInfo['city'],
                'phone'         => '',
                'wx_account'    => '',
                'type'          => 3,
                'state'         => 1,
                'create_time'   => time()
            ];
            $id = Db::table('user')->insertGetId($saveData);
            $locationData['id'] = $id;
            if (!$id) {
                exception('注册失败');
            }
            $saveData['id'] = $id;
            $result = $saveData;
        } else {
            $locationData['id'] = $userInfo->id;
            $result = $userInfo->toArray();
        }
        if (!empty($locationData['lat']) && !empty($locationData['lng'])) {
            (new UserLocation())->save($locationData);
        }
        return $result;
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