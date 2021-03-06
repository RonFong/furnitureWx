<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/27 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\Product as CoreProduct;
use app\common\model\ProductColor;
use app\common\model\ProductPrice;
use app\common\validate\BaseValidate;
use think\Cache;
use think\Db;
use think\Request;


class Product extends CoreProduct
{
    /**
     * 发布产品
     * @param $saveData
     * @return mixed
     * @throws \app\lib\exception\BaseException
     */
    public function saveData($saveData)
    {
        Db::startTrans();
        try {
            $colors = $saveData['colors'];
            unset($saveData['colors']);
            $saveData['factory_id'] = user_info('group_id');
            $saveData['number'] = $this->createGoodsNumber();
            //产品详情，去除空值
            foreach ($saveData['details'] as $k => $v) {
                if (empty(trim($v['content']))) {
                    unset($saveData['details'][$k]);
                }
            }
            $saveData['details'] = json_encode($saveData['details']);
            $saveData['min_price'] = $this->getMinPrice($colors);
            $saveData['review_status'] = 0;
            //获取排序号
            if (Request::instance()->method() == 'POST') {
                $sort = $this->where(['factory_id' => user_info('group_id'), 'classify_id' => $saveData['classify_id']])
                    ->order('sort desc')
                    ->limit(1)
                    ->value('sort') ?? 0;
                $saveData['sort'] = $sort + 1;
            }
            $this->save($saveData);

            if (!empty($saveData['id'])) {
                $this->delColor($saveData['id']);
            }
            foreach ($colors as $k => $color) {
                if (empty($color) && empty($color['img'])) {
                    continue;
                }
                $colorId = Db::table('product_color')->insertGetId([
                    'product_id'    => $this->id,
                    'color'         => $color['color'],
                    'img'           => $color['img'],
                    'sort'          => $k
                ]);
                foreach ($color['prices'] as $price) {
                    Db::table('product_price')->insert([
                        'color_id'      => $colorId,
                        'product_id'    => $this->id,
                        'configure'     => $price['configure'],
                        'trade_price'   => $price['trade_price']
                    ]);
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
        return $this->id;
    }

    /**
     * 获取最低价
     * @param $colors
     * @return mixed
     * @throws \Exception
     */
    protected function getMinPrice($colors)
    {
        $prices = [];
        foreach ($colors as $v) {
            if (empty($v['prices'])) {
                exception('请至少填写一个产品组合及价格');
            }
            foreach ($v['prices'] as $vv) {
                array_push($prices, $vv['trade_price']);
            }
        }
        asort($prices);
        return $prices[0];
    }


    /**
     * 按分类获取产品列表
     * @param $classifyId
     * @param $page
     * @param $row
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListByClassify($classifyId, $page, $row)
    {
        $list = $this->where('classify_id', $classifyId)
            ->field('id, factory_id, name, number, model, is_on_shelves, popularity')
            ->order('sort')
            ->page($page, $row)
            ->select();
        $list = collection($list)->toArray();
        foreach ($list as $k => $v) {
            $colorInfo = (new ProductColor())
                ->field('id, img')
                ->where('product_id', $v['id'])
                ->order('sort')
                ->limit(0,1)
                ->find();
            $list[$k]['img'] = $colorInfo['img'];
             $tradePrice = (new ProductPrice())
                ->where('color_id', $colorInfo['id'])
                ->order('id')
                ->value('trade_price');
            $list[$k]['retail_price'] = round($tradePrice * config('system.price_ratio'));

            //非本店用户或商家，不显示批发价
            $list[$k]['trade_price'] = $this->isShowPrice($v['factory_id']) ? round($tradePrice) : 0;

            $reviewInfo = Db::table('product_review_status')->where('product_id', $v['id'])->order('id desc')->find();
            $list[$k]['review_status'] = $reviewInfo['status'] ?? 0;
            $list[$k]['review_remark'] = $reviewInfo['remark'] ?? '99家服务人员将尽快处理，请稍等~';

        }
        return $list;
    }


    /**
     * 产品详情
     * @param $id
     * @param int $shopId  商家门店 id 等于 0 为 厂家产品详情页，非 0 为商城商品详情页
     * @param bool $isAdmin
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($id, $shopId = 0, $isAdmin = false)
    {
        //人气值
//        $cacheTag = user_info('id') . '_product_popularity';
//        if (!Cache::get($cacheTag)) {
            $this->where('id', $id)->setInc('popularity');
//            Cache::set(user_info('id') . '_product_popularity', $id, 10);
//        }

        $info = $this->where('id', $id)
            ->field('id, factory_id, classify_id, goods_classify_id, is_on_shelves, name, brand, number, model, texture, texture_id, style, style_id, function, function_ids, size, size_ids, discounts, details,form,specification')
            ->find()
            ->toArray();
        $details = json_decode($info['details'], true);
        $info['details'] = [];
        foreach ($details as $k => $v) {
            if ($v['type'] == 'img' && strpos($v['content'], 'x-oss-process') === false) {
                $v['content'] .= '?x-oss-process=image/resize,m_lfit,w_1920,g_center';
            }
            $info['details'][] = $v;
        }
        $info['is_collect'] = Db::table('relation_goods_collect')->where(['user_id' => user_info('id'), 'goods_id' => $id])->find() ? 1 : 0;
        $info['is_in_blacklist'] = 0;
        if (user_info('type') == 2) {
            $info['is_in_blacklist'] = Db::table('relation_goods_blacklist')->where(['shop_id' => user_info('group_id'), 'goods_id' => $id])->find() ? 1 : 0;
        }

        $otherInfo = Db::table('factory')
            ->where('id', $info['factory_id'])
            ->field('admin_user, factory_name, factory_province, factory_city, factory_district, sales_province, sales_city, sales_district')
            ->find();
        $info['factory_name'] = $otherInfo['factory_name'];
        $info['deliver_address'] = $otherInfo['sales_province'] . ' ' . $otherInfo['sales_city'] . ' ' . $otherInfo['sales_district'];
        $info['factory_address'] = $otherInfo['factory_province'] . ' ' . $otherInfo['factory_city'] . ' ' . $otherInfo['factory_district'];

        $info['colors'] = (new ProductColor())->where('product_id', $id)->field('id, color, img')->order('sort')->select();
        if ($shopId) {
            $priceRate = $this->getProductPriceRate($id, $shopId);    // 零售价商家自定义倍率
            $shopInfo = (new Shop())->where('id', $shopId)->field('admin_user, shop_name')->find();
            $info['shop_user_id'] = $shopInfo['admin_user'];
            $info['shop_name'] = $shopInfo['shop_name'];
        } else {
            // 在厂家门店打开产品详情页， 聊天对象为该厂家
            $info['factory_user_id'] = $otherInfo['admin_user'];
            $info['factory_name'] = $otherInfo['factory_name'];
        }
        $isShowPrice = $isAdmin || $this->isShowPrice($info['factory_id']);
        foreach ($info['colors'] as $k => $v) {
            //显示图片宽高
            if ($isAdmin) {
                $imageSize = get_image_size($v['img']);
                $info['colors'][$k]['width'] = $imageSize['width'];
                $info['colors'][$k]['height'] = $imageSize['height'];
            }
            //限定最大图片宽度为 1920
            if (strpos($info['colors'][$k]['img'], 'x-oss-process') === false) {
                $info['colors'][$k]['img'] .= '?x-oss-process=image/resize,m_lfit,w_1920,g_center';
            }

            $prices = (new ProductPrice())->where('color_id', $v->id)->field("id, configure, round(trade_price) as trade_price")->select();
            foreach ($prices as $kk => $vv) {
                $retailPrice = format_price($vv->trade_price * config('system.price_ratio'));
                $prices[$kk]->retail_price = $retailPrice;
                $prices[$kk]->shop_retail_price = isset($priceRate) ? format_price($priceRate * $vv['trade_price']) ?? 0 : 0;

                if (!$isShowPrice) {
                    $prices[$kk]->trade_price = 0;   // 批发价， 非 后台 admin 及当前商家时，设置为0 （隐藏）
                }
            }
            $info['colors'][$k]->prices = $prices;
            unset($info['colors'][$k]->id);
        }
        unset($info['colors'][$k]->id);
        return $info;
    }


    /**
     * 产品删除
     * @param $id
     * @return bool|string
     */
    public function del($id)
    {
        Db::startTrans();
        try {
            $this->where('id', $id)->update(['delete_time' => time()]);
            $colorIds = (new ProductColor())->where(['product_id' => $id])->column('id');
//            ProductColor::destroy(['product_id' => $id]);
            (new ProductColor())->where(['product_id' => $id])->delete();
            ProductPrice::destroy(function ($query) use ($colorIds) {
               $query->where('color_id', 'in', $colorIds);
            });
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return true;
    }


    /**
     * 跟换产品分类
     * @param $id
     * @param $classifyId
     * @return bool
     */
    public function changeClassify($id, $classifyId)
    {
        $this->where('id', $id)->update(['classify_id' => $classifyId]);
        return true;
    }


    /**
     * 更改产品排序
     * @param $id
     * @param $sortAction
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sort($id, $sortAction)
    {
        $product = $this->get($id);
        $model = $this->where(['factory_id' => user_info('group_id'), 'classify_id' => $product->classify_id])->limit(1);
        if ($sortAction == 'inc') {
            $changeProduct = $model->where('sort', '<', $product->sort)->order('sort desc')->find();
        } else {
            $changeProduct = $model->where('sort', '>', $product->sort)->order('sort')->find();
        }
        if ($changeProduct) {
            list($product->sort, $changeProduct->sort) = [$changeProduct->sort, $product->sort];
            $product->save();
            $changeProduct->save();
        }
        return true;
    }


    /**
     * 获取商城商品列表
     * @param $param
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($param)
    {
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
            //非当前商家，不显示出厂价
            if (user_info('type') != 2 || user_info('group_id') != $param['shop_id']) {
                $list[$k]['trade_price'] = 0;
            }
        }
        return $list;
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
        if (array_key_exists('goods_classify_id', $param) && $param['goods_classify_id']) {
            $model->where("goods_classify_id", $param['goods_classify_id']);
        }
        if (array_key_exists('style', $param) && $param['style']) {
            $model->where("style_id in ({$param['style']})");
        }
        if (array_key_exists('function', $param) && $param['function']) {
            $model->where("function_ids like '%{$param['function']}%'");
        }
        if (array_key_exists('size', $param) && $param['size']) {
            $model->where("size_ids like '%{$param['size']}%'");
        }
        if (array_key_exists('texture', $param) && $param['texture']) {
            $model->where("texture_id in ({$param['texture']})");
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
     * 软删除产品图片及其价格配置
     * @param $productId
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function delColor($productId)
    {
        $colorIds = Db::table('product_color')->where('product_id', $productId)->column('id');
        Db::table('product_color')->where('product_id', $productId)->update(['delete_time' => time()]);
        Db::table('product_price')->where('color_id', 'in', $colorIds)->update(['delete_time' => time()]);
    }

    /**
     * 生成产品编号
     * @return string
     */
    protected function createGoodsNumber()
    {
        return strtoupper(dechex(10000 + $this->count()));
    }

    /**
     * 是否显示批发价
     * @param $factoryId
     * @return bool
     */
    protected function isShowPrice($factoryId)
    {
//        var_dump(user_info('group_id'), $factoryId);
//        die;
        //非本店用户或商家，不显示批发价
        if ((user_info('type') == 1 && user_info('group_id') == $factoryId) || user_info('type') == 2) {
            return true;
        }
        return false;
    }
}