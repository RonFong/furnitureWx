<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/22
// +----------------------------------------------------------------------


namespace app\common\model;


class Music extends Model
{
    /**
     * 根据分类id获取音乐列表
     * @param $categoryId
     * @param $page
     * @param $row
     * @return false|static[]
     * @throws \think\exception\DbException
     */
    public function getByCategory($categoryId, $page, $row)
    {
        return self::all(function ($query) use ($categoryId, $page, $row){
            $query->where(['state' => 1, 'category_id' => $categoryId])
                ->field('id, name, author, link, img')
                ->page($page, $row)
                ->order('sort');
        });
    }

    /**
     * 根据音乐名模糊查找
     * @param string $keyword
     * @param array $page
     * @param bool $row
     * @return false|mixed|static[]
     * @throws \think\exception\DbException
     */
    public function query($keyword, $page, $row)
    {
        return self::all(function ($query) use ($keyword, $page, $row){
            $query->where('state', 1)
                ->where('name|author', 'like', "%$keyword%")
                ->field('id, name, author, link, img')
                ->page($page, $row)
                ->order('sort');
        });
    }
}