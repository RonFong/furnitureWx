<?php
namespace app\api\model;

use app\common\model\Factory as CoreFactory;
use think\Db;

class Factory extends CoreFactory
{

    public function saveData($data)
    {
        $data['admin_user'] = user_info('id');
        // 审核暂不审核
        $data['audit_state'] = 1;
//        // 会员分享试用期
//        $data['probation'] = 30;
//        $data['vip_grade'] = 0;
        $this->data($data);
        $registerRes = $this->save();
        if($registerRes){
            Db::name('user')
                ->where('id',$data['admin_user'])
                ->update([
                    'type' => 1,
                    'group_id' => $this->id,
                    'wx_account' => $data['factory_wx']
                ]);
        }
        $result = [
            'store_type'    => 1,
            'id'            => $this->id,
//            'probation'     => $data['probation']
        ];
        return $result;
    }

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