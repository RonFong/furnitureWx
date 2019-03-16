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
            $saveData['details'] = json_encode($saveData['details']);
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
                $colorId = Db::table('product_color')->insertGetId([
                    'product_id'    => $this->id,
                    'color'         => $color['color'],
                    'img'           => $color['img'],
                    'sort'          => $k
                ]);
                foreach ($color['prices'] as $price) {
                    Db::table('product_price')->insert([
                        'color_id'      => $colorId,
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
        foreach ($list as $k => $v) {
            $colorInfo = (new ProductColor())
                ->field('id, img')
                ->where('product_id', $v['id'])
                ->order('sort')
                ->limit(1)
                ->find();
            $list[$k]['img'] = $colorInfo['img'];

             $tradePrice = (new ProductPrice())
                ->where('color_id', $colorInfo['id'])
                ->order('id')
                ->value('trade_price');
            $list[$k]['retail_price'] = round($tradePrice * config('system.price_ratio'));

            //非本店用户或商家，不显示批发价
            $list[$k]['trade_price'] = $this->isShowPrice($v['factory_id']) ? $tradePrice : 0;

            $reviewInfo = Db::table('product_review_status')->where('product_id', $v['id'])->order('id desc')->find();
            $list[$k]['review_status'] = $reviewInfo['status'] ?? 0;
            $list[$k]['review_remark'] = $reviewInfo['remark'] ?? '99家服务人员将尽快处理，请稍等~';
        }
        return $list;
    }


    /**
     * 产品详情
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($id)
    {
        $info = $this->where('id', $id)
            ->field('id, factory_id, name, number, model, texture, style, function, size, discounts, details')
            ->find()
            ->toArray();
        $info['details'] = json_decode($info['details']);

        $info['colors'] = (new ProductColor())->where('product_id', $id)->field('id, color, img')->order('sort')->select();
        $isShowPrice = $this->isShowPrice($info['factory_id']);
        foreach ($info['colors'] as $k => $v) {
            $prices = (new ProductPrice())->where('color_id', $v->id)->field("configure, trade_price")->select();
            foreach ($prices as $kk => $vv) {
                $prices[$kk]->retail_price = round($vv->trade_price * config('system.price_ratio'));
                if (!$isShowPrice) {
                    $prices[$kk]->retail_price = 0;
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
            self::destroy($id);
            $colorIds = (new ProductColor())->where(['product_id' => $id])->column('id');
            ProductColor::destroy(['product_id' => $id]);
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
        $count = $this->where('factory_id', user_info('group_id'))->whereTime('create_time', 'today')->count();
        $id = user_info('group_id') >= 10 ? user_info('group_id') : '0' . user_info('group_id');
        return $id . date('ymd', time()) . sprintf("%02d",$count);
    }

    /**
     * 是否显示批发价
     * @param $factoryId
     * @return bool
     */
    protected function isShowPrice($factoryId)
    {
        //非本店用户或商家，不显示批发价
        if (user_info('group_id') == $factoryId || user_info('type') == 2) {
            return true;
        }
        return false;
    }
}