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
     */
    public function info()
    {
        $this->currentValidate->goCheck('info');
        try {
            $data = $this->currentModel
                ->info($this->data['product_id'], $this->data['shop_id'], true);
            $data['style'] = Db::table('container_style')->where('id', $data['style_id'])->value('name');
            $data['texture'] = Db::table('container_texture')->where('id', $data['texture_id'])->value('name');
            $size = Db::table('container_size')->where('id', 'in', $data['size_ids'])->column('name');
            $data['size'] = implode(',', $size);
            $function = Db::table('container_function')->where('id', 'in', $data['function_ids'])->column('name');
            $data['size'] = implode(',', $size);
            $data['function'] = implode(',', $function);
            print_r($data);
            die;
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
     * 获取商品筛选选项
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOptions()
    {
        $this->currentValidate->goCheck('getOptions');
        //分类
        $data['classify'] = Db::table('goods_classify')->where('pid', 1)->where('delete_time is null')->field('id, classify_name')->select();
        foreach ($data['classify'] as $k => $v) {
            $data['classify'][$k]['child'] = Db::table('goods_classify')->where('pid', $v['id'])->where('delete_time is null')->field('id, classify_name')->select();
        }
        //风格
        $data['style'] = Db::table('goods_style')->where('is_search_option', 1)->field('id, style_name')->order('sort')->select();
        //材质
        $data['texture'] = Db::table('goods_texture')->where('is_search_option', 1)->field('id, texture_name')->order('sort')->select();
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