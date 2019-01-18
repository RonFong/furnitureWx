<?php

namespace app\api\model;

use app\common\model\Shop as CoreShop;
use app\common\model\UserLocation;
use app\api\service\Popularity;
use think\Db;

class Shop extends CoreShop
{
    /**
     * 范围 （公里）
     * @var int
     */
    protected $distance = 10000;

    /**
     * 获取附近的商家
     * @param array $param
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function nearby($param)
    {
        $shopName = $param['shopName'] ?? '';

        $pageData = format_page($param['page'] ?? 0, $param['row'] ?? 10);

//        $currentShopId = user_info('type') == 2 ? user_info('group_id') : 0;   //如果当前用户为商家，则结果中不包含自己
        $currentShopId = 0;

        $location = (new UserLocation())->where(['user_id' => user_info('id')])->order('id desc')->find();

        $field = "s.id, s.shop_name, s.address, s.img_thumb_small, s.distance";
        $where = "`state` = 1 and `delete_time` is null and `id` <> $currentShopId";
        if ($shopName) {
            $where .= " and shop_name like '%$shopName%'";
        }

        $sql = "select {$field} from (
                select *,(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$location['lng']}-lng)/360),2)+COS(PI()*33.07078170776367/180)* COS(lat * PI()/180)*POW(SIN(PI()*({$location['lat']}-lat)/360),2)))) as distance 
                from `shop` 
                where {$where}) as s 
                where s.distance <= {$this->distance}
                order by s.distance asc limit {$pageData['page']}, {$pageData['row']}";

        $list = Db::query($sql);
        foreach ($list as $k => $v) {
            $list[$k]['popularity_num'] = Db::table('popularity')->where(['object_type' => 2, 'object_id' => $v['id']])->value('SUM(value)') ?? 0;
            $list[$k]['distance'] = $v['distance'] >= 1 ? round($v['distance'], 1) . '公里' : ($v['distance'] * 1000 <= 100 ? '100米内' : round($v['distance'] * 1000) . '米');
        }
        return $list;
    }

    /**
     * 商家首页信息
     * @param $shopId
     * @return null|static
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function homePageData($shopId)
    {
        $info = self::get($shopId);
        if (!$info) {
            exception('此商家不存在或信息异常');
        }
        $info->classify = Db::table('shop_commodity')
            ->where('shop_id', $shopId)
            ->where('delete_time is null')
            ->field('id, classify_name')
            ->order('sort,create_time')
            ->select();
        $info->homeContent = (new HomeContent())->details($shopId, 2);
        //商家关注
        if ($shopId == user_info('group_id')) {
            //不能关注自己
            $info->is_collect = -1;
        } else {
            $isCollect = Db::table('relation_shop_collect')
                ->where(['user_id' => user_info('id'), 'shop_id' => $shopId])
                ->find();
            $info->is_collect = $isCollect ? 1 : 0;
        }
        //人气值
        Popularity::increase($shopId, 2);

        return $info;
    }

}