<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/12 
// +----------------------------------------------------------------------


namespace app\api\service;
use app\api\model\Goods;
use app\api\model\GoodsRetailPrice;
use app\common\model\RelationFactoryBlacklist;
use app\common\model\RelationGoodsBlacklist;
use app\common\model\RelationShopBlacklist;
use app\api\model\User;

/**
 * 商城商品
 * Class StoreGoods
 * @package app\api\service
 */
class StoreGoods
{
    /**
     * 当前商城的所属商家
     * @var
     */
    private static $belongToShop = 0;

    /**
     * 商城的访客， false 则为该商家自己
     * @var
     */
    private static $isSelf = false;

    /**
     * 页码
     * @var int
     */
    private static $page = 1;

    /**
     * 每页条目数
     * @var int
     */
    private static $row = 10;

    /**
     * 排序
     * @var string
     */
    private static $orderBy = '';

    private static $retailPrice;

    /**
     * 设置当前商城的所属商家
     * @param $shopId
     * @return $this
     */
    public function setBelongToShop($shopId)
    {
        self::$belongToShop = $shopId;
        self::checkIsSelf();

        //获取此商家的零售价增幅
        $ratio = (new GoodsRetailPrice())->where(['shop_id' => $shopId, 'goods_id' => 0])->value('ratio');
        self::$retailPrice = $ratio ?? config('system.price_ratio');

        return $this;
    }

    /**
     * 设置 商城的访客是否为此商家本人
     */
    private static function checkIsSelf()
    {
        $shopId = (new User())->where(['id' => user_info('id'), 'type' => 2])->value('group_id');
        self::$isSelf = ((int) self::$belongToShop) == $shopId;
    }

    /**
     * 设置页码
     * @param $page
     * @param $row
     * @return $this
     */
    public function setPage($page, $row)
    {
        self::$page = $page;
        self::$row = $row;
        return $this;
    }

    /**
     * 获取首页商品列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function  getHomeList()
    {
        return self::getGoodsBySort();
    }


    /**
     * 获取黑名单中的厂家id 和 商品id
     * @return mixed
     */
    private static function getBlacklist()
    {
        $map = [
            'shop_id'   => self::$belongToShop
        ];
        //拉黑当前商家的
        $factoryBlacklist = (new RelationFactoryBlacklist())->where($map)->column('factory_id');

        //被当前商家拉黑的
        $shopBlacklist = (new RelationShopBlacklist())->where($map)->column('factory_id');

        $blacklist['factory_id'] = array_unique(array_merge($factoryBlacklist, $shopBlacklist));

        //当前商家拉黑的商品
        $blacklist['goods_id'] = (new RelationGoodsBlacklist())->where($map)->column('goods_id');
        return $blacklist;
    }

    /**
     * 商城首页商品列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function getGoodsBySort()
    {
        //TODO 排序算法 ， 暂无

        $blacklist = self::getBlacklist();

        $map = [
            'a.audit_state'     => 3,
            'a.state'           => 1,
            'a.delete_time'     => null,
            'b.state'           => 1,
            'b.audit_state'     => 1,
            'b.delete_time'     => null,
            'a.id'              => ['not in', $blacklist['goods_id']],
            'b.id'              => ['not in', $blacklist['factory_id']],
        ];

        $fields = self::$isSelf ? 'a.model_no' : '';
        $shopId = self::$belongToShop;

        $data = (new Goods())
            ->alias('a')
            ->join('factory b', 'a.factory_id = b.id')
            ->join('popularity c', 'a.id = c.object_id and object_type = 3', 'LEFT')
            ->join('goods_color d', 'a.id = d.goods_id', 'LEFT')
            ->join('goods_factory_price e', 'a.id = e.goods_id', 'LEFT')
            ->join('goods_retail_price f', "a.id = f.goods_id and f.shop_id = $shopId", 'LEFT')
            ->where($map)
            ->field("a.goods_name, a.goods_no, SUM(c.value) as popularity, d.img, e.price, f.amount")
            ->field($fields)
            ->group('a.id, c.object_id')
            ->order(self::$orderBy)
            ->page(self::$page, self::$row)
            ->select();

        $list = [];
        foreach ($data as $v) {
            $v['img'] = get_thumb_img($v['img']);
            $v['retail_price'] = $v['amount'] ?? $v['price'] * self::$retailPrice;
            if (!self::$isSelf) {
                unset($v['price']);
            }
            unset($v['amount']);
            array_push($list, $v);
        }
        return $list;
    }
}