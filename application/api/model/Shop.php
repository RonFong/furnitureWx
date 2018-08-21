<?php

namespace app\api\model;

use app\api\service\Site;
use app\common\model\Shop as CoreShop;
use think\Db;

class Shop extends CoreShop
{
    public function saveData($data = [])
    {
        // 获取经纬度
        if($data['province'] == $data['city']){
            $address = $data['province'].$data['district'].$data['town'].$data['address'];
            $vague_address = $data['province'].$data['district'];
        }else{
            $address = $data['province'].$data['city'].$data['district'].$data['town'].$data['address'];
            $vague_address = $data['province'].$data['city'].$data['district'];
        }
        $site = new Site();
        $lat_lng = $site->getLatLngDetail($address,$data['province']);
        if(empty($lat_lng)){
            // 模糊搜索
            $lat_lng = $site->getLatLngDetail($address,$data['province']);
            if(empty($lat_lng)){
                return ['success' => false,'msg' => '地址不清晰','data' => []];
            }
        }
        $data['lat'] = $lat_lng['lat'];
        $data['lng'] = $lat_lng['lng'];
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
        return ['success' => true,'msg' => '','data' => $result];
    }

    public function getShopInfo($admin_user)
    {
        $shopInfo = $this
            ->field(['id','shop_name','shop_img','province','city','district','town','address','shop_wx','wx_code','shop_phone'])
            ->where('admin_user',$admin_user)
            ->find();
        return $shopInfo;

    }
}