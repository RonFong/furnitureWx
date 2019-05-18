<?php
/**
 * Created by PhpStorm.
 * User: 50xiuer
 * Date: 2019/4/30
 * Time: 20:58
 */

namespace app\admin\model;

use app\common\model\FactoryProductClassify;
use app\common\model\Product as CoreProduct;
use app\common\model\ProductColor;


class Product extends CoreProduct
{
    public function getFactoryNameAttr($value, $data)
    {
        return (new Factory())->where('id', $data['factory_id'])->value('factory_name');
    }

    /**
     * 在商城的分类
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getGoodsClassifyNameAttr($value, $data)
    {
        if (!$data['goods_classify_id']) {
            return '/';
        }
        return (new GoodsClassify())
            ->where('id', $data['goods_classify_id'])
            ->value('classify_name');
    }

    /**
     * 厂家产品分类
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getClassifyNameAttr($value, $data)
    {
        $classifyName =  (new FactoryProductClassify())->where([
            'id'   => $data['classify_id'],
            ])->value('classify_name');
        return $classifyName ?? '/';
    }

    public function getImageAttr($value, $data)
    {
        return (new ProductColor())->where('product_id', $data['id'])->order('sort')->limit(0, 1)->value('img');
    }


}