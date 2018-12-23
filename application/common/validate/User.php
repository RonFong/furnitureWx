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

namespace app\common\validate;

class User extends BaseValidate
{
    protected $rule = [
        'id'                => 'require',            //用户ID
        'user_name'         => 'require|checkLength',                   //昵称
        'group_id'          => 'require|number|groupTypeExits',            //所属厂/商主体
        'group_type'        => 'require|in:1,2',            //所属主体类型
        'phone'             => 'require|isPhoneNo|unique:user',         //手机号
        'type'              => 'require|in:1,2,3',          //用户类型
        'wx_account'        => 'unique:user',    //微信账号
        'wx_openid'         => 'require',     //微信唯一ID
        'avatar'            => 'require',
    ];

    protected $message = [
        'id.require'            => '用户ID不能为空',
        'id.number'             => '用户ID错误',
        'user_name.require'     => '用户名不能为空',
        'group_id.require'      => '用户所属厂/商ID不能为空',
        'phone.require'         => '手机号不能为空',
        'phone.unique'          => '手机号已被绑定账号',
        'type.require'          => '用户类型不能为空',
        'type.in'               => '用户类型参数错误',
        'wx_account.require'    => '用户微信号不能为空',
        'wx_openid.require'     => '用户微信Openid不能为空',
        'wx_account.unique'     => '此微信号已注册',
        'wx_openid.unique'      => '此opendid已注册',
        'avatar.require'        => '头像地址不能为空',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected $scene = [
        //新用户授权小程序获取账号信息 （用户注册）
        'create'   => [
//            'user_name',
            //'type'          => 'require|in:3',
            //'wx_account'    => 'unique:user',
            'wx_openid',
        ],

        //更新用户信息
        'update'    => [
            'id',
            'user_name',
            'phone' =>  'isPhoneNo|unique:user',
            'type'  => 'in:1,2,3',
            'state' => 'in:0,1',
        ],

        'delete'    => [
            'id',
        ],

        'select'    => [
            'id'            => 'number',
            'type'          => 'in:1,2,3',
            'phone'         => 'isPhoneNo',
            'group_id'      => 'number|groupTypeExits',
            'group_type'    => 'in:1,2'
        ],
        'changeAvatar'  => [
            'avatar'
        ],
        'changeName'    => [
            'user_name' => 'checkLength'
        ],
    ];


    /**
     * 校验昵称长度
     * @param $value
     * @return bool|string
     */
    protected function checkLength($value, $role, $data)
    {
        $strlen = strlen(trim($data['userName']));
        if ($strlen < 1) {
            return '请输入昵称';
        }
        if ($strlen > 7) {
            return '昵称不能超过7个字';
        }
        return true;
    }
}