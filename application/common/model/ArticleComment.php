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

use traits\model\SoftDelete;

/**
 * 文章评论
 * Class ArticleComment
 * @package app\common\model
 */
class ArticleComment extends Model
{
    use SoftDelete;

    /**
     * 根据 user_id 获取关联用户信息
     * @return \think\model\relation\HasOne
     */
    public function appendUserName()
    {
        return $this->hasOne('User', 'id', 'user_id')->bind('user_name');
    }



}