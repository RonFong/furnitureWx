<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\common\model\ProductRetailPrice;
use think\Db;
use think\Request;
use app\api\model\Product as ProductModel;
use app\common\validate\Goods as GoodsValidate;

/**
 * 商城商品
 * Class Goods
 * @package app\api\controller\v1
 */
class Goods extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ProductModel();
        $this->currentValidate = new GoodsValidate();
    }

    /**
     * 获取商城列表
     */
    public function getList()
    {
        $this->currentValidate->goCheck('getList');
        try {
            $this->result['data'] = $this->currentModel
                ->getList($this->data);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 商品详情
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function info()
    {
        $this->currentValidate->goCheck('info');
        try {
            $data = $this->currentModel
                ->info($this->data['product_id'], $this->data['shop_id'], true);
            //当前商家设置的零售价倍率
            $data['local_rate'] = Db::table('product_retail_price')
                ->where(['shop_id' => $this->data['shop_id'], 'product_id' => $this->data['product_id']])
                ->value('rate') ?? '';
            $data['global_rate'] = Db::table('product_retail_rate')
                    ->where(['shop_id' => $this->data['shop_id']])
                    ->value('rate') ?? config('system.price_ratio');
            $this->result['data'] = $data;
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 设置自定义零售价
     * @return \think\response\Json
     */
    public function setRetailPrice()
    {
        $this->currentValidate->goCheck('setRetailPrice');
        try {
            $this->data['price'] = $this->data['retail_price'];
            $retailPrice = (new ProductRetailPrice())->get([
                'shop_id' => $this->data['shop_id'],
                'configure_id' => $this->data['configure_id']
            ]);
            if ($retailPrice) {
                $this->data['id'] = $retailPrice->id;
            }
            (new ProductRetailPrice())->save($this->data);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 201);
    }

    /**
     * 获取分类
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getClassifyList()
    {
        //分类
        $data['classify'] = Db::table('goods_classify')
            ->where('pid', 1)
            ->where('delete_time is null')
            ->field('id, classify_name')
            ->order('sort_num asc')
            ->select();
        foreach ($data['classify'] as $k => $v) {
            $data['classify'][$k]['child'] = Db::table('goods_classify')
                ->where('pid', $v['id'])
                ->where('delete_time is null')
                ->field('id, classify_name')
                ->order('sort_num asc')
                ->select();
        }
        $this->result['data'] = $data;
        return json($this->result, 200);
    }

    /**
     * 按分类获取属性
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttrByClassify()
    {
        $classifyId = $this->data['classify_id'];
        $attrName = ['style', 'texture', 'function', 'size'];
        $data = [];
        foreach ($attrName as $v) {
            $data[$v] = Db::table('goods_'.$v)
                ->alias('a')
                ->join("container_{$v} b", "a.{$v}_id = b.id")
                ->where('a.goods_classify_id', $classifyId)
                ->field('b.id, b.name')
                ->select();
        }
        $this->result['data'] = $data;
        return json($this->result, 200);
    }

    /**
     * 根据分类id 获取 功能 和 尺寸 （规格） 选项
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSizeAndFunctionByClassify()
    {
        $this->currentValidate->goCheck('getSizeAndFunctionByClassify');
        //功能
        $data['function'] = Db::table('goods_function')->where('goods_classify_id', $this->data['classify_id'])->where('is_search_option', 1)->field('id, function_name')->order('sort')->select();
        //尺寸
        $data['size'] = Db::table('goods_size')->where('goods_classify_id', $this->data['classify_id'])->where('is_search_option', 1)->field('id, size_describe')->order('sort')->select();
        $this->result['data'] = $data;
        return json($this->result, 200);
    }
}