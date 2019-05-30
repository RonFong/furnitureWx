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

    protected static $defaultAvatar = 'http://api-multimedia.oss-cn-shenzhen.aliyuncs.com/image/default/2019-01-20/1547964763903.jpg?x-oss-process=image/resize,m_lfit,w_400/crop,x_0,y_0,h_400,g_center';


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
        if (!empty($wxUserInfo['lng'])) {
            $locationData = [
                'lng'           => sprintf("%.6f", $wxUserInfo['lng']),
                'lat'           => sprintf("%.6f", $wxUserInfo['lat']),
                'create_time'   => time()
            ];
        }
        if (!$userInfo) {
            $saveData = [
                'wx_openid'     => self::$openid,
                'avatar'        => empty($wxUserInfo['avatarUrl']) ? self::$defaultAvatar : $wxUserInfo['avatarUrl'],
                'user_name'     => self::emojiEncode($wxUserInfo['nickName']),
                'group_id'      => 0,
                'gender'        => $wxUserInfo['gender'],
                'province'      => $wxUserInfo['province'],
                'city'          => $wxUserInfo['city'],
                'phone'         => '',
                'wx_account'    => '',
                'type'          => 3,
                'state'         => 1,
                'login_num'     => 1,
                'last_login_time'   => time(),
                'create_time'   => time()
            ];
            $id = Db::table('user')->insertGetId($saveData);
            $locationData['user_id'] = $id;
            if (!$id) {
                exception('注册失败');
            }
            $saveData['id'] = $id;
            $result = $saveData;
        } else {
            $locationData['user_id'] = $userInfo->id;
            //累加登录次数,记录最后登录时间
            if (time() - $userInfo->last_login_time > 60) {
                $user = new User();
                $user->where('id', $userInfo->id)->setInc('login_num', 1);
                $user->where('id', $userInfo->id)->update(['last_login_time' => time()]);
            }
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


    /**
     * Emoji原形转换为String
     * @param string $content
     * @return string
     */
    public static function emojiEncode($content)
    {
        return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
            return addslashes($str[0]);
        }, json_encode($content)));
    }

    /**
     * Emoji字符串转换为原形
     * @param string $content
     * @return string
     */
    public static function emojiDecode($content)
    {
        return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, json_encode($content)));
    }

}