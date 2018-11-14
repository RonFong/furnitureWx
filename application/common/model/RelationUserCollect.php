<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/16 
// +----------------------------------------------------------------------


namespace app\common\model;

/**
 * 用户关注商家
 * Class RelationShopCollect
 * @package app\common\model
 */
class RelationUserCollect extends Model
{

    /**
     * 我关注的用户
     * @param $page
     * @param $row
     * @return mixed
     */
    public function myCollect($page, $row)
    {
        $data = $this->alias('a')
            ->join('user b', 'a.other_user_id = b.id')
            ->where(['a.user_id' => user_info('id'), 'a.delete_time' => null])
            ->field('b.id, b.user_name, b.avatar')
            ->group('a.other_user_id')
            ->order('a.create_time desc')
            ->page($page, $row)
            ->select();

        $list['total'] = self::where('user_id', user_info('id'))->count();
        $list['list'] = array_map(['self', 'isTogether'], $data);

        return $list;
    }

    /**
     * 是否相互关注
     * @param $v
     * @return mixed
     */
    private function isTogether($v)
    {
        $result = self::get(['user_id' => $v['id'], 'other_user_id' => user_info('id')]);
        $v['is_together'] = empty($result) ? false : true;
        return $v;
    }

    /**
     * 关注我的
     * @param $page
     * @param $row
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collectMe($page, $row)
    {
        $data['total'] = self::where('user_id', user_info('id'))->count();
        $data['list'] = $this->alias('a')
            ->join('user b', 'a.user_id = b.id')
            ->where(['a.other_user_id' => user_info('id'), 'a.delete_time' => null])
            ->field('b.id, b.user_name, b.avatar')
            ->group('a.user_id')
            ->order('a.create_time desc')
            ->page($page, $row)
            ->select();

        $list['list'] = array_map(['self', 'isTogether'], $data);
        return $data;
    }
}