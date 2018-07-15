<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | Date: 2018/6/30 0030 19:39
// +----------------------------------------------------------------------

namespace app\common\model;


use think\Db;
use traits\model\SoftDelete;

class Article extends Model
{
    use SoftDelete;

    public function content()
    {
        return $this->hasMany('ArticleContent', 'article_id', 'id');
    }

    /**
     * 删除文章
     * @param $id
     * @return bool|int|string
     */
    public function deleteData($id)
    {
        Db::startTrans();
        try {
            $articleID = self::destroy($id);
            if (!$articleID)
                exception('此文章已被删除或不存在');
            //关联删除，无法应用软删除
            //非关联删除
            $result = (new ArticleContent())::destroy(['article_id' => $id]);
            if (!$result)
                exception('删除失败');
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return true;
    }
}