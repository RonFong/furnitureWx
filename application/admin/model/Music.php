<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-{2018} http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/9/1 16:45
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Music as CoreMusic;
use think\Db;

class Music extends CoreMusic
{
    /**
     * 获取状态名称
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getStateTextAttr($value, $data)
    {
        $value = isset($data['state']) ? $data['state'] : $value;
        return $value==1 ? '启用' : "禁用";
    }

    /**
     * 获取分类名称
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getCategoryNameAttr($value, $data)
    {
        $value = isset($data['category_id']) ? $data['category_id'] : $value;
        return Db::name('music_category')->where('id', $value)->value('category_name');
    }
}