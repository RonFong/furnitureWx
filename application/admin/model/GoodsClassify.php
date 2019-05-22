<?php
/**
 * Created by PhpStorm.
 * User: 50xiuer
 * Date: 2019/4/30
 * Time: 20:30
 */

namespace app\admin\model;


use app\common\model\GoodsClassify as CoreGoodsClassify;

/**
 * 商城商品分类
 * Class GoodsClassify
 * @package app\admin\model
 */
class GoodsClassify extends CoreGoodsClassify
{
    public function getPidNameAttr($value)
    {
        return $this->where('id', $value)->value('classify_name') ?? '顶级分类';
    }

    public function getGoodsNumAttr($value)
    {
        return (new Product())->where('goods_classify_id', $value)->count();
    }

    /**
     * 获取父级分类
     * @param $value
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getClassifyListOption($value = 0)
    {
        $list = $this->where('pid', 1)->field(true)->select();
        $list = \Tree::get_option_tree($list, $value, 'classify_name', 'id');
        return $list;
    }
}