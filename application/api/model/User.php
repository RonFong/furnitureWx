<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/15 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\User as CoreUser;
use think\Db;


class User extends CoreUser
{

    /**
     * 名片信息
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($id)
    {
        $user['main_user'] = Db::table('user')
            ->where('id', $id)
            ->field('user_name, avatar, type, group_id')
            ->find();
        if (!$user['main_user']) {
            exception('此用户不存在');
        }
        $user['secondary_user'] = [];
        if (user_info('type') == 2) {
            //商家用户
            $group = Db::table('shop')
                ->where('id', $user['main_user']['group_id'])
                ->field('shop_name, shop_phone, shop_wx, qr_code_img, qr_code_img_thumb, user_name, phone, wx_account, license_code')
                ->find();
            $main = [
                'group_type'        => 2,
                'shop_name'         => $group['shop_name'],
                'phone'             => $group['shop_phone'],
                'wx_account'        => $group['shop_wx'],
                'qr_code_img'       => $group['qr_code_img'],
                'qr_code_img_thumb' => $group['qr_code_img_thumb']
            ];
            //联系人信息
            $user['main_user'] += $main;

            //负责人信息
            $user['secondary_user'] = [
                'group_type'        => 2,
                'shop_name'         => $group['shop_name'],
                'phone'             => $group['phone'],
                'wx_account'        => $group['wx_account'],
                'qr_code_img'       => $group['license_code'],
                'qr_code_img_thumb' => $group['license_code']
            ];
        }
        if (user_info('type') == 1) {
            //厂家用户
            $group = Db::table('factory')
                ->where('id', $user['main_user']['group_id'])
                ->field('factory_name, factory_phone, factory_wx, qr_code_img, qr_code_img_thumb, user_name, phone, wx_account, license_code')
                ->find();
            $main = [
                'group_type'        => 1,
                'factory_name'      => $group['factory_name'],
                'phone'             => $group['factory_phone'],
                'wx_account'        => $group['factory_wx'],
                'qr_code_img'       => $group['qr_code_img'],
                'qr_code_img_thumb' => $group['qr_code_img_thumb']
            ];
            //联系人信息
            $user['main_user'] += $main;

            //负责人信息
            $user['secondary_user'] = [
                'group_type'        => 1,
                'factory_name'      => $group['factory_name'],
                'phone'             => $group['phone'],
                'wx_account'        => $group['wx_account'],
                'qr_code_img'       => $group['license_code'],
                'qr_code_img_thumb' => $group['license_code']
            ];
        }
        return $user;
    }
}