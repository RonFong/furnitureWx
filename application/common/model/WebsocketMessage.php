<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class WebsocketMessage extends Model
{

    protected function getTimeAttr($value)
    {
        return time_format_for_humans($value);
    }

    /**
     * 当前用户与某用户的聊天记录
     * @param $fromId
     * @param $toId
     * @param int $page
     * @param int $row
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function logWithUser($fromId, $toId, $page = 1, $row = 10)
    {

        $list = $this->alias('w')
            ->join('user f_u', "f_u.id = w.from_id")
            ->join('user t_u', "t_u.id = w.to_id")
            ->where(['w.from_clear' => 0, 'w.state' => 1])
            ->where(function ($query) use ($fromId, $toId){
                $query->whereOr(['w.from_id' => $fromId, 'w.to_id' => $toId]);
            })
            ->order('w.id desc')
            ->field('f_u.id as from_id, f_u.user_name as from_user_name, f_u.avatar as from_avatar, t_u.id as to_id, t_u.user_name as to_user_name, t_u.avatar as to_avatar, w.message, w.read as is_read, send_time as time')
            ->page($page, $row)
            ->select();
        return $list;
    }
}