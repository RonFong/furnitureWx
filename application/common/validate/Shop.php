<?php

namespace app\common\validate;

class Shop extends BaseValidate
{
    protected $rule = [
        'id'                => 'require',                   // 用户ID
        'shop_name'         => 'require|checkShopWx|checkUserType',       // 门店名称
        'shop_contact'      => 'require',                   // 门店联系人
        'shop_phone'        => 'require|isPhoneNo',         // 门店电话
        'province'          => 'require',            // 省
        'city'              => 'require',            // 市
        'district'          => 'require',            // 区
        'address'           => 'require|chsDash',           // 详细地址
        'shop_img'          => 'require',                   // 门头照片
        'category_id'       => 'require|number',                    // 经营类别(大类)
        'category_child_id' => 'require',                   // 经营类别(子类)
        'shop_wx'           => 'require',               //微信号
        'lat'               => 'require',               //经纬度
        'lng'               => 'require',
        'shop_img_thumb'    => 'require',

    ];

    protected $message = [
        'admin_user.require'    => '请先授权',
        'admin_user.number'     => '请先授权',
        'shop_name.require'     => '请填写门店名称',
        'shop_contact.require'  => '请填写联系人',
        'shop_phone.require'    => '请填写电话',
        'province'              => '请填写门店地区',
        'city'                  => '市不能为空',
        'district'              => '区/县不能为空',
        'address'               => '请填写详细地址',
        'shop_img.require'      => '门头照片不能为空',
        'category_id'           => '请选择经营类型',
        'category_child_id.require'      => '请选择经营类型',
        'lat.require'           => 'lat 经纬度不能为空',
        'lng.require'           => 'lng 经纬度不能为空',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected $scene = [
        //新用户授权小程序获取账号信息 （用户注册）
        'create'   => [
            'shop_name',
            'shop_contact',
            'shop_phone',
            'province',
            'city',
            'district',
            'town',
            'address',
            'shop_img',
            'shop_img_thumb',
            'lat',
            'lng'

        ],
        'info' => []
    ];


    protected function checkShopWx($value,$rule,$data)
    {
        if(empty($data['shop_wx']) && empty($data['wx_code'])){
            return '请填写门店微信';
        }
        return true;
    }


    protected function checkUserType($value)
    {
        if (user_info('type') != 0) {
            return '当前用户已创建 厂/商 门店';
        }
        return true;
    }
}