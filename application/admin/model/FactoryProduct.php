<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\FactoryProduct as CoreFactoryProduct;
use think\Db;

class FactoryProduct extends CoreFactoryProduct
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
        $item = ['0'=>'待审核', '1'=>'审核通过', '2'=>'审核不通过'];
        return isset($item[$value]) ? $item[$value] : "";
    }

    /**
     * 获取分类名称
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getClassifyNameAttr($value, $data)
    {
        $value = isset($data['classify_id']) ? $data['classify_id'] : $value;
        return Db::name('group_classify')->where('id', $value)->value('classify_name');
    }

    /**
     * 获取厂家名称
     * @param string $value
     * @param string $data
     * @return mixed
     */
    public function getFactoryNameAttr($value, $data)
    {
        $value = isset($data['factory_id']) ? $data['factory_id'] : $value;
        return Db::name('user')->where('id', $value)->value('user_name');
    }


}