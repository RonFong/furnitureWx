<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Goods as CoreGoods;
use think\Db;
use think\Request;

class Goods extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreGoods();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        $param = $this->request->param();

        //分类名称列表
        $classifyList = Db::name('group_classify')->select();
        $this->assign('classifyList', $classifyList);

        //页面类型：厂家/商城
        $this->assign('web_type', isset($param['web_type']) ? $param['web_type'] : '');
        return $this->fetch();
    }

    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $map = $this->getDataListMap();
        return $this->currentModel
            ->where($map)
            ->order('id desc')
            ->layTable(['audit_state_text', 'classify_name', 'factory_name']);
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        if (!empty($param['factory_id'])) {
            $map['factory_id'] = $param['factory_id'];//厂家ID
        }
        if (!empty($param['classify_id'])) {
            $map['classify_id'] = $param['classify_id'];//分类
        }
        if (isset($param['state']) && $param['state'] !== '') {
            $map['state'] = $param['state'];//状态
        }
        if (!empty($param['web_type'])) {
            $map['audit_state'] = $param['web_type']=="factory" ? ['<>', 3] : 3;//产品显示规则：厂家（未通过审核），商城（通过审核）
        }
        if (empty($map)) {
            $map[] = ['exp', '1=1'];
        }
        return $map;
    }

    /**
     * 编辑
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $param = $this->request->param();

        if (!empty($param['id'])) {
            $data = $this->currentModel->where('id', $param['id'])->find();
            if (empty($data)) {
                $this->error('信息不存在');
            }
            $data = $data->toArray();
            $this->assign('data', $data);

            //商品颜色
            $data_color = $this->currentModel->goodsColor()->where('goods_id',$param['id'])->order('id asc')->select();
            $this->assign('data_color', $data_color);

            //商品优惠券
            $data_coupon = $this->currentModel->goodsCoupon()->where('goods_id',$param['id'])->order('id asc')->select();
            $this->assign('data_coupon', $data_coupon);
        }
        //厂家分类名称列表
        $classifyList = Db::name('group_classify')->select();
        $this->assign('classifyList', $classifyList);

        //商品分类名称列表
        $storeClassifyList = Db::name('store_classify')->where('parent_id', '<>', 0)->field('id,name')->select();
        $this->assign('storeClassifyList', $storeClassifyList);

        //商品所属顶级分类名称列表
        $propertyList = Db::name('store_classify_property')->field('id,property_name')->select();
        $this->assign('propertyList', $propertyList);

        //厂家列表
        $factoryList = Db::name('factory')->field('id,factory_name')->select();
        $this->assign('factoryList', $factoryList);

        //页面类型：厂家/商城
        $this->assign('web_type', isset($param['web_type']) ? $param['web_type'] : '');

        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'Goods');
        if ($result !== true) {
            $this->error($result);
        }

        try {
            //保存数据
            $this->currentModel->save($param);
            if (!empty($param['color_list'])) {
                $this->currentModel->goodsColor()->saveAll($param['color_list']);
            }
            if (!empty($param['coupon_list'])) {
                $this->currentModel->goodsCoupon()->saveAll($param['coupon_list']);
            }
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        $this->success('保存成功！', 'edit?id='.$this->currentModel->id);
    }

    /**
     * 获取商品分类列表
     * @param $factory_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getStoreClassify($factory_id)
    {
        $category_id = Db::name('factory')->where('id', $factory_id)->value('category_id');
        return Db::name('store_classify')->where('parent_id', $category_id)->where('state', 1)->field('id,name')->select();
    }

    /**
     * 获取商品分类下的属性列表
     * @param $classify_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getStoreClassifyProperty($classify_id)
    {
        $map = [];
        $ids = get_parent_ids($classify_id, 'store_classify', $map);
        $where = [];
        $where['id'] = ['in', array_push($ids,$classify_id)];
        $where['state'] = 1;
        return Db::name('store_classify_property')->where($where)->field('id,property_name,type')->select();
    }

    public function deleteColor($id)
    {
        Db::name('goods_color')->where('id', $id)->delete();
        $this->success('删除成功');
    }

    public function deleteCoupon($id)
    {
        Db::name('goods_coupon')->where('id', $id)->delete();
        $this->success('删除成功');
    }
}

