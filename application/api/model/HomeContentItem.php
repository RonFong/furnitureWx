<?php
namespace app\api\model;

use app\common\model\HomeContentItem as CoreHomeContentItem;

class HomeContentItem extends CoreHomeContentItem
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
    public function getContentAttr($value)
    {
        return $this->emojiDecode($value);
    }

    public function setContentAttr($value)
    {
        return $this->emojiEncode($value);
    }
}