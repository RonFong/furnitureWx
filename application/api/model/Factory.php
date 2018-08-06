<?php
namespace app\api\model;

use app\common\model\Factory as CoreFactory;

class Factory extends CoreFactory
{

    public function getFactoryList($data)
    {

        $field  = [
            'id',
            'factory_contact',
            'factory_phone',
            'factory_wx',
            'wx_code',
            'province',
            'city',
            'district',
            'town',
            'address',
            'factory_name',
            'factory_address',
            'category_id',
            'category_child_id',
            'user_name',
            'phone',
            'license_code',
            'factory_img',
        ];
        $where = [
            'state' => 1
        ];
        $result = $this->field($field)->where($where)->page($data['page'], $data['row'])->select();

        return $result;
    }

    public function getFactoryProduct($data)
    {
        $field  = [
            'id',
            'classify_id',
            'sort',
            'music',
            'record',
        ];
        $where = [
            'state' => 1
        ];
        $model = new FactoryProduct();
        $result = $model->field($field)
                        ->with(['groupClassify'])
                        ->where($where)
                        ->page($data['page'], $data['row'])
                        ->select();

        return $result;

    }

    public function factoryInfo($data)
    {
        $field  = [
            '*',
        ];
        $where = [
            'admin_user' => $data['userId']
        ];
        $result = $this->field($field)
            ->where($where)
            ->find();

        return $result;
    }


}