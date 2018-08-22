<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/21 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\UserProposed as CoreUserProposed;
use think\Db;

class UserProposed extends CoreUserProposed
{

    /**
     * 获取推荐列表
     * @param $page
     * @param $row
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProposedList($page, $row)
    {
        $data = Db::table('user_proposed')
            ->alias('a')
            ->join('user b', 'a.proposed_id = b.id')
            ->where('a.user_id', user_info('id'))
            ->field('b.id, b.type, b.group_id, from_unixtime(a.create_time, "%Y-%m-%d") as create_time')
            ->page($page, $row)
            ->order('a.create_time desc')
            ->select();
        $list = [];
        $proposedMoney = config('system.proposed_money');
        foreach ($data as $v) {
            if ($v['type'] == 1) {
                $v['group_name'] = Db::table('factory')->where('id', $v['group_id'])->value('factory_name');
                $v['proposed_money'] = $proposedMoney['factory'];
            } elseif ($v['type'] == 2) {
                $v['group_name'] = Db::table('shop')->where('id', $v['group_id'])->value('shop_name');
                $v['proposed_money'] = $proposedMoney['shop'];
            } else {
                continue;
            }
            unset($v['id']);
            array_push($list, $v);
        }
        return $list;
    }
}