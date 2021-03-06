<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\ArticleClassify as CoreArticleClassify;

class ArticleClassify extends CoreArticleClassify
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
     * 获取上级分类名称
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getParentTextAttr($value, $data)
    {
        $value = isset($data['parent_id']) ? $data['parent_id'] : $value;
        return $this->where('id', $value)->value('classify_name');
    }

}