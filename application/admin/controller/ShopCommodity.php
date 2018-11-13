<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\ShopCommodity as CoreShopCommodity;
use app\admin\model\ShopCommodityItem;
use think\Db;
use think\Request;

class ShopCommodity extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreShopCommodity();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        //商家列表
        $shopList = Db::name('shop')->field('id,shop_name')->select();
        $this->assign('shopList', $shopList);
        return $this->fetch();
    }

    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $map = $this->getDataListMap();
        return $this->currentModel->where($map)->order('id desc')->layTable(['shop_name', 'classify_name']);
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        if (!empty($param['shop_id'])) {
            $map['shop_id'] = $param['shop_id'];//商户id
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
            $ShopCommodityContent = new ShopCommodityItem();
            $content = $ShopCommodityContent->where('commodity_id', $param['id'])->order('sort')->select();
            $this->assign('content', $content);

            $data = $data->toArray();
            $this->assign('data', $data);

        }

        //商家列表
        $shopList = Db::name('shop')->field('id,shop_name')->select();
        $this->assign('shopList', $shopList);

        //分类列表
        $classifyList = Db::name('group_classify')->field('id,classify_name')->select();
        $this->assign('classifyList', $classifyList);

        //音乐列表
        $musicList = Db::name('music')->field('id,name')->select();
        $this->assign('musicList', $musicList);

        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        try {
            //保存数据
            $this->currentModel->save($param);
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        $this->success('保存成功！', 'edit?id='.$this->currentModel->id);
    }

}

