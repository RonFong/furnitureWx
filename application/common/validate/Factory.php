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


use think\Db;

class Factory extends BaseValidate
{
    protected $rule = [
        'id'                => 'require|number',
        'admin_user'        => 'require|number',
        'sales_contact'     => 'require|length:2,5',
        'sales_wx'          => 'require',
        'sales_phone'       => 'require|number|length:11',
        'sales_province'    => 'require',
        'sales_city'        => 'require',
        'sales_district'    => 'require',
        'address'           => 'require',
        'lat'               => 'require',
        'lng'               => 'require',
        'license_img'       => 'require',
        'factory_wx'        => 'require',
        'factory_contact'   => 'require|length:2,5',
        'factory_phone'     => 'require|number|length:11',
        'factory_province'  => 'require',
        'factory_city'      => 'require',
        'factory_district'  => 'require',
        'deliver_province'  => 'require',
        'deliver_city'      => 'require',
        'deliver_district'  => 'require',


    ];

    protected $message = [
        'sales_contact.require'     => '请填写门店联系人姓名',
        'sales_contact.length'      => '请填写正确的门店联系人姓名',
        'sales_wx.require'          => '请填写门店联系人微信号',
        'sales_phone.require'       => '请填写门店联系人电话',
        'sales_phone.number'        => '请填写正确的门店联系人电话',
        'sales_phone.length'        => '请填写正确的门店联系人电话',
        'sales_province.require'    => '请填写门店所在省',
        'sales_city.require'        => '请填写门店所在市',
        'sales_district.require'    => '请填写门店所在区县',
        'license_img.require'       => '请上传营业执照',
        'lat.require'               => '请在地图上选中门店所在地址',
        'lng.require'               => '请在地图上选中门店所在地址',
        'address.require'           => '请填写门店详细地址',
        'factory_wx.require'        => '请输入负责人微信号',
        'factory_contact.length'    => '请填写正确的负责人姓名',
        'factory_phone.require'     => '请填写负责人电话',
        'factory_phone.number'      => '请填写正确的负责人电话',
        'factory_phone.length'      => '请填写正确的负责人电话',
        'factory_province.require'  => '请填写工厂所在省',
        'factory_city.require'      => '请填写工厂所在市',
        'factory_district.require'  => '请填写工厂所在区县',
        'deliver_province.require'  => '请填写发货地址省',
        'deliver_city.require'      => '请填写发货地址市',
        'deliver_district.require'  => '请填写发货区地址县',

    ];


    protected $scene = [
        'create'    => [
            'admin_user'    => 'require|number|isGroupUser',
            'sales_contact',
            'sales_wx',
            'sales_phone',
            'sales_province',
            'sales_city',
            'sales_district',
            'address',
            'lat',
            'lng',
        ],
    ];

    /**
     * 用户是否已有所属门店，如果是，则不可再创建
     * @param $value
     * @return bool|string
     */
    protected function isGroupUser($value)
    {
        if (user_info('type') != 3) {
            return '用户已有所属门店';
        }
        return true;
    }
}