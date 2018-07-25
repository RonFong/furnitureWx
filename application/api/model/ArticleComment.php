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

namespace app\api\model;

use app\common\model\ArticleComment as CoreArticleComment;

/**
 * 文章评论 （圈子）
 * Class ArticleComment
 * @package app\api\model
 */
class ArticleComment extends CoreArticleComment
{
    public function saveData($data)
    {
        $data['user_id'] = user_info('id');
        if ($this->save($data)) {
            $result = [
                'user_name'     => user_info('user_name'),
                'content'       => $data['content'],
                'create_time'   => date('m-d H:i', time())
            ];
            //所回复的评论的发布者
            if (array_key_exists('parent_id', $data)) {
                $info = self::with('appendUserName')->where('id', $this->data['parent_id'])->find();
                $result['parent_user_name'] = $info->user_name;
            }
             return $result;
        }
        return false;
    }
}