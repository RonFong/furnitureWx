<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/22
// +----------------------------------------------------------------------


namespace app\common\model;


class MusicCategory extends Model
{
    /**
     * 获取音乐分类
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryList()
    {
        return $this
            ->alias('a')
            ->join('music b', 'a.id = b.category_id and b.state = 1')
            ->where('a.state', 1)
            ->field('a.id, a.category_name, count(b.id) as quantity')
            ->group('a.id')
            ->order('a.sort')
            ->select();
    }
}