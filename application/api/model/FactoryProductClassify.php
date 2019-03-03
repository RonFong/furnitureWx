<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/20 
// +----------------------------------------------------------------------


namespace app\api\model;


use app\common\model\FactoryProductClassify as CoreFactoryProductClassify;

/**
 * 厂家产品分分类
 * Class FactoryProductClassify
 * @package app\api\model
 */
class FactoryProductClassify extends CoreFactoryProductClassify
{
    /**
     * 删除分类
     * @param $id
     * @return bool|int|string
     * @throws \think\exception\DbException
     */
    public function del($id)
    {
        $productCount = (new Product())->where('classify_id', $id)->count();
        if ($productCount > 0) {
            return $productCount;
        }
        $classify = self::get($id);
        $classify->delete();
        return true;
    }
}