<?php

namespace app\api\model;

use app\api\service\Site;
use app\common\model\Shop as CoreShop;
use app\common\model\UserLocation;
use think\Db;

class Shop extends CoreShop
{
    /**
     * 范围 （公里）
     * @var int
     */
    protected $distance = 10;

    /**
     * 获取附近的商家
     * @param array $param
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function nearby($param)
    {
        $shopName = $param['shopName'] ?? '';
        $page = $param['page'] ?? 0;
        $row = $param['row'] ?? 10;
        //如果当前用户为商家，则结果中不包含自己
        $currentShopId = user_info('type') == 2 ? user_info('group_id') : 0;
        $location = UserLocation::get(user_info('id'));

        $field = "s.id, s.shop_name, s.address, s.shop_img_thumb, s.distance";
        $where = "`id` <> $currentShopId and `state` = 1 and `delete_time` is null";
        if ($shopName) {
            $where .= " and shop_name like '%$shopName%'";
        }

        $sql = "select {$field} from (
                select *,(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$location->lng}-lng)/360),2)+COS(PI()*33.07078170776367/180)* COS(lat * PI()/180)*POW(SIN(PI()*({$location->lat}-lat)/360),2)))) as distance 
                from `shop` 
                where {$where}) as s 
                where s.distance <= {$this->distance}
                order by s.distance asc limit {$page}, {$row}";

        $list = Db::query($sql);
        foreach ($list as $k => $v) {
            $list[$k]['distance'] = $v['distance'] >= 1 ? round($v['distance'], 1) . '公里' : round($v['distance'], 2) . '米';
        }
        return $list;
    }

}