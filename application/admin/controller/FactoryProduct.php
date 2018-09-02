<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\FactoryProduct as CoreFactoryProduct;
use think\Db;
use think\Request;

class FactoryProduct extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreFactoryProduct();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        //分类名称列表
        $classifyList = Db::name('group_classify')->select();
        $this->assign('classifyList', $classifyList);
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
            ->layTable(['state_text', 'classify_name', 'factory_name']);
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
        $classifyList = Db::name('group_classify')->select();
        $this->assign('classifyList', $classifyList);

        //厂家列表
        $userList = Db::name('user')->select();
        $this->assign('userList', $userList);
        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'FactoryProduct');
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

