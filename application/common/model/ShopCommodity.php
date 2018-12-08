<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/11/13 
// +----------------------------------------------------------------------


namespace app\common\model;

/**
 * 商家商品
 * Class ShopCommodity
 * @package app\common\model
 */
class ShopCommodity extends Model
{
    /**
     * 视频截帧
     * @param $value
     * @return string
     */
    public function getVideoSnapshotAttr($value)
    {
        if ($value) {
            return $value . config('system.video_snapshot');
        }
        return '';
    }

    /**
     * 高度自适应
     * @param $value
     * @return string
     */
    public function getVideoSnapshotAutoAttr($value)
    {
        if ($value) {
            return $value . config('system.video_snapshot_auto');
        }
        return '';
    }
}