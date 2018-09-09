<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Model;

class ShopCommodityContent extends Model
{
    /**
     * 文章内容转义
     * @param $value
     * @return string
     */
    public function getTextAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
}