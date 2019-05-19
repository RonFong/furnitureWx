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
    protected $distance = 100;

    public function __construct($data = [])
    {
        parent::__construct($data);
        //客服账号不限制距离
        if (user_info('is_service_account')) {
            $this->distance = 10000;
        }
    }

    /**
     * 获取附近的商家
     * @param array $param
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function nearby($param)
    {
        $groupName = $param['group_name'] ?? '';

        $pageData = format_page($param['page'] ?? 0, $param['row'] ?? 10);

        $location = (new UserLocation())->where(['user_id' => user_info('id')])->order('id desc')->find();

        $field = "s.group_id, s.group_type, s.group_name, s.distance";
        $where = "group_type = 2 and `state` = 1 and `audit_state` = 1 and `delete_time` is null";
        if ($groupName) {
            $where .= " and group_name like '%$groupName%'";
        }

        $sql = "select {$field} from (
                select *,(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$location['lng']}-lng)/360),2)+COS(PI()*33.07078170776367/180)* COS(lat * PI()/180)*POW(SIN(PI()*({$location['lat']}-lat)/360),2)))) as distance 
                from `group_nearby` 
                where {$where}) as s 
                where s.distance <= {$this->distance}
                order by s.distance asc limit {$pageData['page']}, {$pageData['row']}";

        $list = Db::query($sql);
        foreach ($list as $k => $v) {
            $list[$k]['popularity_num'] = Db::table('popularity')->where(['object_type' => $v['group_type'], 'object_id' => $v['group_id']])->value('SUM(value)') ?? 0;
            $list[$k]['distance'] = $v['distance'] >= 1 ? round($v['distance'], 1) . '公里' : ($v['distance'] * 1000 <= 100 ? '100米内' : round($v['distance'] * 1000) . '米');
            $info = $this->getGroupInfo($v['group_id'], $v['group_type']);
            $list[$k]['address'] = $info['address'];
            $list[$k]['img_thumb_small'] = $info['img_thumb_small'];
            $list[$k]['shop_id'] = $v['group_id'];
            $list[$k]['shop_name'] = $v['group_name'];
            $list[$k]['shop_img'] = $info['img_thumb_small'];
        }
        return $list;
    }

    /**
     * 获取门店信息
     * @param $id
     * @param $type
     * @return array|false|\PDOStatement|string|\think\Model
     */
    private function getGroupInfo($id, $type)
    {
        if ($type == 1) {
            $table = 'factory';
        }
        if ($type == 2) {
            $table = 'shop';
        }
        return Db::table($table)->where('id', $id)->field('address, img_thumb_small')->find();
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

        return $info;
    }

}