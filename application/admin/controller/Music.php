<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Music as CoreMusic;
use think\Db;
use think\Request;

class Music extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreMusic();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        $categoryList = Db::name('music_category')->select();
        $this->assign('categoryList', $categoryList);
        return $this->fetch();
    }

    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $map = $this->getDataListMap();
        return $this->currentModel->where($map)->order('id desc')->layTable(['state_text', 'category_name']);
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        if (!empty($param['category_id'])) {
            $map['category_id'] = $param['category_id'];//分类名称
        }
        if (!empty($param['name'])) {
            $map['name'] = ['like', '%' . $param['name'] . '%'];//音乐名称
        }
        if (!empty($param['author'])) {
            $map['author'] = ['like', '%' . $param['author'] . '%'];//艺术家名
        }
        if (isset($param['state']) && $param['state'] !== '') {
            $map['state'] = $param['state'];//状态
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
        }
        //分类名称列表
        $categoryList = Db::name('music_category')->select();
        $this->assign('categoryList', $categoryList);
        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'Music');
        if ($result !== true) {
            $this->error($result);
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

