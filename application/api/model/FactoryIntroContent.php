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

use app\common\model\FactoryIntroContent as CoreFactoryIntroContent;

/**
 * 厂家简介内容
 * Class FactoryIntroItem
 * @package app\api\model
 */
class FactoryIntroContent extends CoreFactoryIntroContent
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