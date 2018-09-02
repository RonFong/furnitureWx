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
     * @return array
     * @throws \app\lib\exception\BaseException
     */
    public function getCollectList($param = [], $page = 1, $row = null)
    {
        //关联数据
        $data = [
            'factory' => [
                'name'  => '厂家',
                'list'  => []
            ],
            'shop'  => [
                'name'  => '经销商',
                'list'  => []
            ],
            'goods' => [
                'name'  => '商品',
                'list'  => []
            ],
            'default'  => []
        ];

        if (array_key_exists('category', $param) && !array_key_exists($param['category'], $data)) {
            (new BaseValidate())->error(Response::QUERY_ERROR);
        } else if (!array_key_exists('category', $param) || !array_key_exists($param['category'], $data)) {
            //查询出所有数据后，再分页
            $sumPage = $page == 0 ? 1 : $page ;
            $sumRow = $row;
            $page = 1;
            $row = null;
        }

        $map = ['a.user_id' => user_info('id')];

        if (!array_key_exists('category', $param) || $param['category'] == 'factory') {
            $model = Db::table('relation_factory_collect');
            $data['factory']['list'] = $this->getFactory($model, $map, $page, $row);
        }

        if (!array_key_exists('category', $param) || $param['category'] == 'shop') {
            $model = Db::table('relation_shop_collect');
            $data['factory']['list'] = $this->getShop($model, $map, $page, $row);
        }

        if (!array_key_exists('category', $param) || $param['category'] == 'goods') {
            $model = Db::table('relation_goods_collect');
            $data['goods']['list'] = $this->getGoods($model, $map, $page, $row);
        }

        //获取所有收藏，按时间排序
        if (!array_key_exists('category', $param) || !array_key_exists($param['category'], $data)) {

            $list = array_merge($data['goods']['list'], $data['shop']['list'], $data['factory']['list']);
            $data['factory']['list'] = $data['shop']['list'] = $data['goods']['list'] = [];

            $array = array_column($list, 'create_time');
            array_multisort($array, SORT_DESC, $list);

            //按分页，切割数组返回
            $data['default'] = array_slice($list, $sumPage == 1 ? 0 : ($sumPage - 1) * $sumRow, $sumRow);
        }

        return $data;
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
     * @param $model
     * @param $map
     * @param $page
     * @param $row
     * @return mixed
     */
    protected function getFactory($model, $map, $page, $row)
    {
        return $model
            ->alias('a')
            ->join('factory b', 'a.factory_id = b.id')
            ->where($map)
            ->field('b.id, b.factory_name as name, b.factory_img as img, b.state, ifnull(b.delete_time, 0) as deleted, 1 as type')
            ->field("from_unixtime(a.create_time, '%Y-%m-%d') as create_time")
            ->order('a.id desc')
            ->page($page, $row)
            ->select();
    }

    /**
     * 根据驱动表获取商家数据
     * @param $model
     * @param $map
     * @param $page
     * @param $row
     * @return mixed
     */
    protected function getShop($model, $map, $page, $row)
    {
        return $model
            ->alias('a')
            ->join('shop b', 'a.shop_id = b.id')
            ->where($map)
            ->field('b.id, b.shop_name as name, b.shop_img as img, b.state, ifnull(b.delete_time, 0) as deleted, 2 as type')
            ->field("from_unixtime(a.create_time, '%Y-%m-%d') as create_time")
            ->order('a.id desc')
            ->page($page, $row)
            ->select();
    }

    /**
     * 根据驱动表获取商品数据
     * @param $model
     * @param $map
     * @param $page
     * @param $row
     * @return mixed
     */
    protected function getGoods($model, $map, $page, $row)
    {
        return $model
            ->alias('a')
            ->join('goods b', 'a.goods_id = b.id')
            ->join('goods_color c', 'a.goods_id = c.goods_id')
            ->where($map)
            ->field('b.id, b.goods_name as name, b.state, ifnull(b.delete_time, 0) as deleted, c.img as img, 3 as type')
            ->field("from_unixtime(a.create_time, '%Y-%m-%d') as create_time")
            ->group('b.id')
            ->order('a.id desc')
            ->page($page, $row)
            ->select();
    }
}