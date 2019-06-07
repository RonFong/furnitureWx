<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/5 
// +----------------------------------------------------------------------


namespace app\common\model;


class GoodsAttrVal extends Model
{
    protected function getProductNumAttr($value, $data)
    {
        return (new Product())->where("find_in_set({$data['id']}, attr_ids)")->count();
    }

    protected function setSortNumAttr($value, $data)
    {
        if (empty($data['id']) && !empty($data['attr_id'])) {
            return $this->where('attr_id', $data['attr_id'])->count();
        }
        return $value;
    }
}