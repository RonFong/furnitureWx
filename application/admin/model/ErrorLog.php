<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Model as CommonModel;

class ErrorLog extends CommonModel
{
    /**
     * ip 地址转换
     * @param $value
     * @return string
     */
    public function getIpAttr($value)
    {
        return long2ip($value);
    }

}