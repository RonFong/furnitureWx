<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/11/22 
// +----------------------------------------------------------------------


namespace app\api\service;
use think\Cache;
use app\api\model\Popularity as PopularityModel;

/**
 * 人气值
 * Class Popularity
 * @package app\api\service
 */
class Popularity
{

    /**
     * 厂、商、商品 增长人气值
     * @param $groupId
     * @param $groupType
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function increase($groupId, $groupType)
    {
        //人气
        if ($groupId != user_info('group_id')) {
            $key = user_info('id')."_{$groupType}_".$groupId;
            //  false 为第一次点击查看
            $isFirst = Cache::get($key);
            //距离上次点击查看的间隔时间是否在限制内
            $validTime = $isFirst ? (time() - $isFirst) > config('system.popularity_time') : 1;
            if ($validTime) {
                $data = [
                    'object_id'     => $groupId,
                    'object_type'   => $groupType,
                    'date'          => date('Ymd', time()),
                    'month'         => date('Ym', time())
                ];
                $info = (new PopularityModel())->where($data)->find();
                if ($data) {
                    $data['id'] = $info['id'];
                    $data['value'] = $info['value'] + 1;
                } else {
                    $data['value'] = 1;
                }
                (new PopularityModel())->save($data);
                Cache::set($key, time());
            }

        }
    }
}