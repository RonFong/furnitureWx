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
    }
}