<?php

namespace app\common\validate;

class Shop extends BaseValidate
{
    protected $rule = [
        'id'                => 'require',               // 用户ID
        'admin_user'        => 'require|number',        // 管理员id
        'contact'           => 'require',               // 门店联系人
        'store_phone'       => 'require|isPhoneNo',     // 门店电话
        'store_wx'          => 'require',               // 门店微信
        'province'          => 'require|number',        // 省
        'city'              => 'require|number',        // 市
        'district'          => 'require|number',        // 区
        'town'              => 'require',               // 乡镇街道
        'address'           => 'require|chsDash',       // 详细地址
        'shop_img'          => 'require',               // 门头照片
        'category'          => 'number',                // 经营类别
    ];

    protected $message = [
        'id.require'            => '用户ID不能为空',
        'admin_user.require'    => '管理员不能为空',
        'admin_user.number'     => '管理员id错误',
        'contact.require'       => '门店联系人不能为空',
        'store_phone.require'   => '门店电话不能为空',
        'store_wx.require'      => '门店微信不能为空',
        'province.require'      => '地区不能为空',
        'province.number'       => '地区id错误',
        'city.require'          => '市不能为空',
        'city.number'           => '市id错误',
        'district.require'      => '区不能为空',
        'district.number'       => '区id错误',
        'town.require'          => '乡镇街道不能为空',
        'address.require'       => '请填写详细地址',
        'address.chsDash'       => '地址格式错误',
        'shop_img.require'      => '门头照片不能为空',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected $scene = [
        //新用户授权小程序获取账号信息 （用户注册）
        'register'   => [
            'admin_user',
            'contact',
            'store_phone',
            'store_wx',
            'province',
            'city',
            'district',
            'town',
            'address',
            'shop_img',
            'category',
        ],
    ];
}