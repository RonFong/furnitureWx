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



class Factory extends BaseValidate
{
    protected $rule = [
        'id'                => 'require|number',
        'admin_user'        => 'require|number',
        'factory_name'      => 'require|length:2,20',
        'sales_contact'     => 'require|length:2,5',
        'sales_wx'          => 'require',
        'sales_phone'       => 'require|length:11|isPhoneNo',
        'sales_province'    => 'require',
        'sales_city'        => 'require',
        'sales_district'    => 'require',
        'address'           => 'require',
        'lat'               => 'require',
        'lng'               => 'require',
        'license_img'       => 'require',
        'factory_wx'        => 'require',
        'factory_contact'   => 'require|length:2,5',
        'factory_phone'     => 'length:11|isPhoneNo',
        'factory_province'  => 'require',
        'factory_city'      => 'require',
        'factory_district'  => 'require',
        'deliver_province'  => 'require',
        'deliver_city'      => 'require',
        'deliver_district'  => 'require',
        'license_img'       => 'require',


    ];

    protected $message = [
        'factory_name.require'      => '请填写厂家名称',
        'factory_name.length'       => '厂家名称需在2~20个字内',
        'sales_contact.require'     => '请填写门店联系人姓名',
        'sales_contact.length'      => '请填写正确的门店联系人姓名',
        'sales_wx.require'          => '请填写门店联系人微信号',
        'sales_phone.require'       => '请填写门店联系人电话',
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
        'factory_phone.length'      => '请填写正确的负责人电话',
        'factory_province.require'  => '请填写工厂所在省',
        'factory_city.require'      => '请填写工厂所在市',
        'factory_district.require'  => '请填写工厂所在区县',
        'deliver_province.require'  => '请填写发货地址省',
        'deliver_city.require'      => '请填写发货地址市',
        'deliver_district.require'  => '请填写发货区地址县',
        'license_img'               => '请上传营业执照'
    ];


    protected $scene = [
        'create'    => [
            'factory_name',
            'sales_contact',
            'sales_wx'      => 'require|isGroupUser',
            'sales_phone',
            'sales_province',
            'sales_city',
            'sales_district',
            'address',
            'lat',
            'lng',
            'factory_phone'
        ],
        'supplementInfo' => [
            'factory_wx',
            'factory_contact',
            'factory_phone',
            'factory_province',
            'factory_city',
            'factory_district',
            'deliver_province',
            'deliver_city',
            'deliver_district',
            'license_img'
        ],
        'update'    => [
            'id' => 'require|number|isAdminUser',

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
            return '非普通用户，不可注册';
        }
        return true;
    }


    /**
     * 是否是门店店主
     * @param $value
     * @return bool|string
     * @throws \think\exception\DbException
     */
    protected function isAdminUser($value)
    {
        $info = \app\common\model\Factory::get($value);
        if ($info->admin_user != user_info('id')) {
            return '非本店用户，无权操作';
        }
        return true;
    }
}