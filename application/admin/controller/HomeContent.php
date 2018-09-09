<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\HomeContent as CoreHomeContent;
use app\admin\model\HomeContentItem;
use think\Db;
use think\Request;

class HomeContent extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreHomeContent();//实例化当前模型
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
        return $this->currentModel->where($map)->order('id desc')->layTable(['group_name','group_type_name']);
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
            $HomeContentItem = new HomeContentItem();
            $content = $HomeContentItem->where('commodity_id', $param['id'])->order('sort')->select();
            $this->assign('content', $content);

            $data = $data->toArray();
            $this->assign('data', $data);

        }

        //分类名称列表
        $value_id = !empty($data['parent_id']) ? $data['parent_id'] : 0;
        $classifyList = Db::name('group_classify')->select();
        $classifyList = \Tree::get_option_tree($classifyList, $value_id, 'classify_name', 'id', 'parent_id');
        $this->assign('classifyList', $classifyList);

        $groupList = [];
        if (isset($data['group_type'])) {
            if ($data['group_type'] == 1) {
                $groupList =  Db::name('factory')->field('id,factory_name as name')->select();
            } elseif ($data['group_type'] == 2) {
                $groupList =  Db::name('shop')->field('id,shop_name as name')->select();
            }
        }
        $this->assign('groupList', $groupList);

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

