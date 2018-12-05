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
}