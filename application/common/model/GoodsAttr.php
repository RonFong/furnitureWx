<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/5 
// +----------------------------------------------------------------------


namespace app\common\model;


class GoodsAttr extends Model
{
    protected function getValNumAttr($value, $data)
    {
        return (new GoodsAttrVal())->where('attr_id', $data['id'])->count();
    }

}