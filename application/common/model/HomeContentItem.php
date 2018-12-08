<?php

namespace app\common\model;


class HomeContentItem extends Model
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