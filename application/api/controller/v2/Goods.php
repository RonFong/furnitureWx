<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/7 
// +----------------------------------------------------------------------

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\api\model\Product;
use app\common\model\ProductRetailPrice;
use app\common\model\ProductRetailRate;
use think\Db;
use app\common\validate\Goods as GoodsValidate;

/**
 * 商城商品
 * Class Store
 * @package app\api\controller\v2
 */
class Goods extends BaseController
{

    /**
     * 按分类获取属性
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttrByClassify()
    {
        $attrs = Db::table('goods_classify_attr')
            ->alias('a')
            ->join('goods_attr_val b', 'a.attr_val_id = b.id')
            ->join('goods_attr c', 'b.attr_id = c.id')
            ->where('a.goods_classify_id', $this->data['classify_id'])
            ->field('b.id as enum_id, b.enum_name, c.attr_name, c.id as attr_id, c.sort_num')
            ->order('sort_num asc')
            ->select();
        $data = [];
        foreach ($attrs as $k => $v) {
            $attrId = $v['attr_id'];
            if (!isset($data[$attrId])) {
                $data[$attrId] = ['attr_name' => $v['attr_name'], 'enum_list' => []];
            }
            unset($v['attr_id'], $v['attr_name'], $v['sort_num']);
            array_push($data[$attrId]['enum_list'], $v);

        }
        $this->result['data'] = array_values($data);
        return json($this->result, 200);
    }


    /**
     * 获取商城商品列表
     * @param $param
     * @return mixed
     * @throws \app\lib\exception\BaseException
     */
    public function getList()
    {
        try {
            $param = $this->request->param();
            (new GoodsValidate())->goCheck('getList');
            $model = (new Product())
                ->where([
                    'state'   => 1,
                    'is_on_shelves'   => 1,
                    'review_status'   => 1
                ])
                ->field('id as retail_price, id as img, min_price as trade_price');

            //被拉黑的厂家
            $factoryBlacklistInitiative = Db::table('relation_shop_blacklist')->where('shop_id', $param['shop_id'])->column('factory_id');
            //拉黑当前商家的厂家
            $factoryBlacklistPassivity = Db::table('relation_factory_blacklist')->where('shop_id', $param['shop_id'])->column('factory_id');
            $excludeFactoryIds = array_merge($factoryBlacklistInitiative, $factoryBlacklistPassivity);
            if ($excludeFactoryIds) {
                $factoryIds = implode(',', $excludeFactoryIds);
                $model->where("factory_id not in ('$factoryIds')");
            }
            //被拉黑的商品
            $excludeGoodsIds = Db::table('relation_goods_blacklist')->where('shop_id', $param['shop_id'])->column('goods_id');
            if ($excludeGoodsIds) {
                $goodsIds = implode(',', $excludeGoodsIds);
                $model->where("factory_id not in ('$goodsIds')");
            }

            $model = $this->bindWhere($model, $param);
            $model = $this->bindOrderBy($model, $param);
            $list = $model->field('id, goods_classify_id, name, factory_id, number, model, popularity')
                ->page($param['page'], $param['row'])
    //            ->order('sort_store desc, sort_store_set_time desc')
                ->order('update_time desc')
                ->select();
            foreach ($list as $k => $v) {
                $v['trade_price'] = round($v['trade_price']);
                //非当前商家，不显示出厂价
                if (user_info('type') != 2 || user_info('group_id') != $param['shop_id']) {
                    $list[$k]['trade_price'] = 0;
                }
            }
            $this->result['data'] = $list;
            return json($this->result, 200);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
    }

    /**
     * 构建 where
     * @param $model
     * @param $param
     * @return mixed
     */
    protected function bindWhere($model, $param)
    {
        //搜索关键字
        if (array_key_exists('search_key', $param) && $param['search_key']) {
            $model->where('name', 'like', "%{$param['search_key']}%");
        }
        //分类
        if (array_key_exists('goods_classify_id', $param) && $param['goods_classify_id']) {
            $model->where("goods_classify_id", $param['goods_classify_id']);
        }
        // 属性筛选
        if (array_key_exists('attr_ids', $param) && $param['attr_ids']) {
            $attrIds = explode(',', $param['attr_ids']);
            $likeStr = '';
            foreach ($attrIds as $k => $v) {
                if ($k != 0) {
                    $likeStr .= ' or ';
                }
                //  查 attr_id = 1 时，防止 匹配到 attr_id 包含 1 的其他值
                $likeStr .= " attr_ids like '%\"$v,%' or attr_ids like '%,$v\"%' or attr_ids like '%\"$v\"%' ";
            }
            $model->where($likeStr);
        }
        return $model;
    }

    /**
     * 构建 order by
     * @param $model
     * @param $param
     * @return mixed
     */
    protected function bindOrderBy($model, $param)
    {
        if (array_key_exists('order_by_time', $param) && $param['order_by_time']) {
            $model->order('create_time ' . $param['order_by_time']);
        }
        if (array_key_exists('order_by_popularity', $param) && $param['order_by_popularity']) {
            $model->order('popularity ' . $param['order_by_popularity']);
        }
        if (array_key_exists('order_by_price', $param) && $param['order_by_price']) {
            $model->order('trade_price ' . $param['order_by_price']);
        }
        return $model;
    }

    /**
     * 商家修改商品零售价倍率
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function setRetailPrice()
    {
        $param = $this->request->param();
        try {
            if (user_info('type') != 2) {
                exception('非商家用户不可修改零售价');
            }
            if (empty($param['product_id']) || !is_numeric($param['product_id'])) {
                exception('product_id 错误');
            }
            if (!(new Product())->get($param['product_id'])) {
                exception('该商品不存在');
            }
            if (!empty($param['local_rate']) && !is_numeric($param['local_rate']) && !is_float($param['local_rate'])) {
                exception('当前产品零售价倍率格式错误');
            }
            if (!empty($param['global_rate']) && !is_numeric($param['global_rate']) && !is_float($param['global_rate'])) {
                exception('全局零售价倍率格式错误');
            }
            $param['shop_id'] = user_info('group_id');
            $localModel = (new ProductRetailPrice());
            if (!empty($param['local_rate'])) {
                $param['rate'] = $param['local_rate'];
                $param['id'] = $localModel->value('id');
                $localModel->save($param);
            } else {
                $localModel->where(['shop_id' => user_info('group_id'), 'product_id' => $param['product_id']])->delete();
            }
            $globalModel = (new ProductRetailRate());
            if (!empty($param['global_rate'])) {
                $param['id'] = $globalModel->value('id');
                $param['rate'] = $param['global_rate'];
                $globalModel->save($param);
            } else {
                $globalModel->where(['shop_id' => user_info('group_id')])->delete();
            }
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 201);
    }

}