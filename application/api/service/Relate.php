<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/16 
// +----------------------------------------------------------------------


namespace app\api\service;

use app\common\validate\BaseValidate;
use app\lib\enum\Response;
use think\Db;

/**
 * 产生关联
 * Class Relate
 * @package app\api\service
 */
class Relate
{
    /**
     * 当前操作的模型
     * @var object
     */
    protected $behaviorModel;

    /**
     * Relate constructor.
     * @param null $behaviorModel
     * @throws \app\lib\exception\BaseException
     */
    public function __construct($behaviorModel = null)
    {
        if ($behaviorModel) {
            //写入和删除
            try {
                $class =  '\\app\\common\\model\\' . $behaviorModel;
                $class = new \ReflectionClass($class);
                $this->behaviorModel = $class->newInstanceArgs();
            } catch (\Exception $e) {
                (new BaseValidate())->error($e);
            }
        }
    }

    /**
     * @param $data
     * @return mixed
     * @throws \app\lib\exception\BaseException
     */
    public function save($data)
    {
        try {
            $data['user_id'] = user_info('id');
            $method = $data['type'];
            return $this->$method($data);
        } catch (\Exception $e) {
            (new BaseValidate())->error($e);
        }
    }

    /**
     * 增长
     * @param $data
     * @return string
     */
    protected function inc($data)
    {
        $data['create_date'] = date('Ymd', time());
        $data['create_time'] = time();
        $result = $this->behaviorModel->save($data);
        return $result ?? false;
    }

    /**
     * 减少
     * @param $data
     * @return bool
     */
    protected function dec($data)
    {
        unset($data['type']);
        $result = $this->behaviorModel->where($data)->delete();
        return $result ?? false;
    }


    /**
     * 用户收藏列表
     * @param array $param
     * @param int $page
     * @param null $row
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCollectList($param = [], $page = 1, $row = null)
    {
        if ($param['category'] == 'factory') {
            return $this->getFactory($page, $row);
        }
        if ($param['category'] == 'shop') {
            return $this->getShop($page, $row);
        }
        if ($param['category'] == 'goods') {
            return $this->getGoods($page, $row);
        }
        return [];
    }


    /**
     * 获取黑名单列表
     * @param $param
     * @param int $page
     * @param null $row
     * @return array|bool
     */
    public function getBlackList($param, $page = 1, $row = null)
    {
        if (user_info('type') == 1) {
            //厂家
            $data = [
                'shop' => [
                    'name'  => '经销商',
                    'list'  => []
                ]
            ];
            $map = ['a.factory_id' => user_info('group_id')];
            $model = Db::table('relation_factory_blacklist');
            $data['shop']['list'] = $this->getShop($model, $map, $page, $row);
            return $data;
        }

        if (user_info('type') == 2) {
            //商家
            $data = [
                'factory' => [
                    'name'  => '厂家',
                    'list'  => []
                ],
                'goods' => [
                    'name'  => '商品',
                    'list'  => []
                ],
                'default'  => []
            ];

            if (!array_key_exists('category', $param)) {
                //查询出所有数据后，再分页
                $sumPage = $page == 0 ? 1 : $page ;
                $sumRow = $row;
                $page = 1;
                $row = null;
            }
            $map = ['a.shop_id' => user_info('group_id')];
            if (!array_key_exists('category', $param) || $param['category'] == 'factory') {
                $model = Db::table('relation_shop_blacklist');
                $data['factory']['list'] = $this->getFactory($model, $map, $page, $row);
            }
            if (!array_key_exists('category', $param) || $param['category'] == 'goods') {
                $model = Db::table('relation_goods_blacklist');
                $data['goods']['list'] = $this->getGoods($model, $map, $page, $row);
            }

            //获取所有收藏，按时间排序
            if (!array_key_exists('category', $param) || !array_key_exists($param['category'], $data)) {

                $list = array_merge($data['goods']['list'], $data['factory']['list']);
                $data['factory']['list'] = $data['goods']['list'] = [];

                $array = array_column($list, 'create_time');
                array_multisort($array, SORT_DESC, $list);

                //按分页，切割数组返回
                $data['default'] = array_slice($list, $sumPage == 1 ? 0 : ($sumPage - 1) * $sumRow, $sumRow);
            }

            return $data;
        }

        return false;
    }

    /**
     * 根据驱动表获取厂家数据
     * @param $page
     * @param $row
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getFactory($page, $row)
    {
        return Db::table('relation_factory_collect')
            ->alias('a')
            ->join('factory b', 'a.factory_id = b.id')
            ->where(['a.user_id' => user_info('id'), 'b.state' => 1])
            ->where('b.delete_time is null')
            ->field('b.id, b.factory_name as name, b.factory_img_thumb as img_thumb, factory_img as img')
            ->page($page, $row)
            ->order('a.create_time desc')
            ->select();
    }

    /**
     * 获取收藏的商家
     * @param $page
     * @param $row
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getShop($page, $row)
    {
        return Db::table('relation_shop_collect')
            ->alias('a')
            ->join('shop b', 'a.shop_id = b.id')
            ->where(['a.user_id' => user_info('id'), 'b.state' => 1])
            ->where('b.delete_time is null')
            ->field('b.id, b.shop_name as name, b.shop_img_thumb as img_thumb, shop_img as img')
            ->page($page, $row)
            ->order('a.create_time desc')
            ->select();
    }

    /**
     * 获取收藏的商品
     * @param $page
     * @param $row
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getGoods($page, $row)
    {
        return Db::name('relation_goods_collect')
            ->alias('a')
            ->join('goods b', 'a.goods_id = b.id')
            ->join('goods_color c', 'a.goods_id = c.goods_id')
            ->where(['a.user_id' => user_info('id')])
            ->field('b.id, b.goods_name as name, c.img_thumb, img')
            ->group('b.id')
            ->page($page, $row)
            ->order('a.create_time desc')
            ->select();
    }
}