<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/16 
// +----------------------------------------------------------------------


namespace app\common\model;
use traits\model\SoftDelete;

/**
 * 用户收藏文章
 * Class RelationArticleCollect
 * @package app\common\model
 */
class RelationArticleCollect extends Model
{
    use SoftDelete;
}