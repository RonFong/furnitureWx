<?php
namespace app\api\model;

use app\common\model\Factory as CoreFactory;

class FactoryProduct extends CoreFactory
{

    public function getFactoryList($data)
    {

        $field  = [
            'id',
            'factory_contact',
            'factory_phone',
            'factory_wx',
            'province',
            'city',
            'district',
            'town',
            'address',
            'factory_name',
            'factory_address',
            'user_name',
            'phone',
            'license_code',
            'factory_img',
        ];
        $result = $this->field($field)->page($data['page'], $data['row'])->select();

        return $result;
    }

    public function getFactoryProduct($data)
    {
        $field  = [
            'id',
            'factory_contact',
            'factory_phone',
            'factory_wx',
            'province',
            'city',
            'district',
            'town',
            'address',
            'factory_name',
            'factory_address',
            'user_name',
            'phone',
            'license_code',
            'factory_img',
        ];
        $result = $this->field($field)->where('')->page($data['page'], $data['row'])->select();

        return $result;
    }


}