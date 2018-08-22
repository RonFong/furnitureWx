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
     * @param $categoryId
     * @return false|static[]
     * @throws \think\exception\DbException
     */
    public function getByCategory($categoryId)
    {
        return self::all(function ($query) use ($categoryId){
            $query->where(['state' => 1, 'category_id' => $categoryId])
                ->field('id, name, author, link, img')
                ->order('sort');
        });
    }

    /**
     * 根据音乐名模糊查找
     * @param $name
     * @return false|static[]
     * @throws \think\exception\DbException
     */
    public function query($name)
    {
        return self::all(function ($query) use ($name){
            $query->where('state', 1)
                ->where('name|author', 'like', "%$name%")
                ->field('id, name, author, link, img')
                ->order('sort');
        });
    }
}