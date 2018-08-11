<?php

namespace app\api\model;

use app\common\model\Shop as CoreShop;
use think\Db;

class Shop extends CoreShop
{
    public function saveData($data = [])
    {
        $data['admin_user'] = user_info('id');
        // 审核暂不审核
        $data['audit_state'] = 1;
        // 会员分享试用期
        $data['probation'] = 30;
        $data['vip_grade'] = 0;
        $this->data($data);
        $registerRes = $this->save();
        if($registerRes){
            Db::name('user')
                ->where('id',$data['admin_user'])
                ->update([
                    'type' => 2,
                    'group_id' => $this->id,
                    'wx_account' => $data['shop_wx']
                ]);
        }
        $result = [
            'store_type'    => 2,
            'id'            => $this->id,
            'probation'     => $data['probation']
        ];
        return $result;
    }

    public function selectShop()
    {

    }
}