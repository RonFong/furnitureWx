<?php

namespace app\common\validate;

class Shop extends BaseValidate
{
    protected $rule = [
        'id'                => 'require',                   // 用户ID
        'admin_user'        => 'require|number',            // 管理员id
        'shop_name'         => 'require',                   // 门店名称
        'shop_contact'      => 'require',                   // 门店联系人
        'shop_phone'        => 'require|isPhoneNo',         // 门店电话
        'shop_wx'           => 'checkShopWx',               // 门店微信
        'province'          => 'require|number',            // 省
        'city'              => 'require|number',            // 市
        'district'          => 'require|number',            // 区
        'town'              => 'require',                   // 乡镇街道
        'address'           => 'require|chsDash',           // 详细地址
        'shop_img'          => 'require',                   // 门头照片
        'category_id'       => 'require|number',                    // 经营类别(大类)
        'category_child_id' => 'require',                   // 经营类别(子类)
    ];

    protected $message = [
        'id.require'            => '请先授权',
        'admin_user.require'    => '请先授权',
        'admin_user.number'     => '请先授权',
        'shop_name.require'     => '请填写门店名称',
        'shop_contact.require'  => '请填写联系人',
        'shop_phone.require'    => '请填写电话',
        'province'              => '请填写门店地区',
        'city'                  => '市不能为空',
        'district'              => '区/县不能为空',
        'town'                  => '乡/镇不能为空',
        'address'               => '请填写详细地址',
        'shop_img.require'      => '门头照片不能为空',
        'category_id'           => '请选择经营类型',
        'category_child_id.require'      => '请选择经营类型',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected $scene = [
        //新用户授权小程序获取账号信息 （用户注册）
        'register'   => [
            'admin_user',
            'shop_name',
            'shop_contact',
            'shop_phone',
            'shop_wx',
            'province',
            'city',
            'district',
            'town',
            'address',
            'shop_img',
            'category_id',
            'category_child_id'
        ],
    ];


    protected function checkType($value,$rule,$data)
    {
        if(empty($data['shop_wx']) && empty($data['wx_code'])){
            return '请填写门店微信';
        }
        return true;
    }
}