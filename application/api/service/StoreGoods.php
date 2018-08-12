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

    private static $page = 1;

    private static $row = 10;

    /**
     * 排序
     * @var string
     */
    private static $orderBy = '';

    /**
     * 设置当前商城的所属商家
     * @param $shopId
     * @return $this
     */
    public function setBelongToShop($shopId)
    {
        self::$belongToShop = $shopId;
        self::checkIsSelf();
        return $this;
    }

    /**
     * 设置 商城的访客是否为此商家本人
     */
    private static function checkIsSelf()
    {
        $shopId = (new User())->where(['id' => user_info('id'), 'group_type' => 2])->value('group_id');
        self::$isSelf = self::$belongToShop == $shopId ? false : $shopId;
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
     * @return mixed
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

        $blacklist['factory_id'] = array_merge($factoryBlacklist, $shopBlacklist);

        //当前商家拉黑的商品
        $blacklist['goods_id'] = (new RelationGoodsBlacklist())->where($map)->column('goods_id');
        return $blacklist;
    }


    private static function getGoodsBySort()
    {
        //TODO 排序算法 ， 暂无
        $blacklist = self::getBlacklist();
        $map = [
            'b.state'           => 1,
            'b.audit_state'     => 1,
            'b.delete_time'     => null,
            'b.id'              => ['not in', $blacklist['factory_id']],
            'a.audit_state'     => 3,
            'a.state'           => 1,
            'a.delete_time'     => null,
            'a.id'              => ['not in', $blacklist['goods_id']],
            'c.object_type'     => 3
        ];
        if (self::$visitorUser) {
            $fields = 'a.goods_name, a.goods_no, d.img';
        } else {
            $fields = 'a.goods_name, a.goods_no, a.model_no, d.img';
        }
        $list = (new Goods())
            ->alias('a')
            ->join('factory b', 'a.factory_id = b.id')
            ->join('popularity c', 'a.id = c.object_id')
            ->join('goodsColor d', 'a.id = d.goods_id')
            ->where($map)
            ->field($fields)
            ->order(self::$orderBy)
            ->page(self::$page, self::$row)
            ->select();
        print_r($list);
        die;
        return $list;
    }
}