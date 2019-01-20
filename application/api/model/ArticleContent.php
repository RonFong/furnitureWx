<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/15 
// +----------------------------------------------------------------------
namespace app\api\model;

use app\common\model\ArticleContent as CoreArticleContent;
use think\Cache;
use think\Db;

class ArticleContent extends CoreArticleContent
{
    public function getStyleAttr($value)
    {
        if ($value) {
            return json_decode($value);
        }
        return '';
    }

    /**
     * Emoji 表情符
     * @param $value
     * @return string
     */
    public function getTextAttr($value)
    {
        return $this->emojiDecode($value);
    }

    public function setTextAttr($value)
    {
        return $this->emojiEncode($value);
    }
}