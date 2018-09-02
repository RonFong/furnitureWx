<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\GroupClassify as CoreGroupClassify;
use think\Db;
use think\Request;

class GroupClassify extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreGroupClassify();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        //分类名称列表
        $classifyList = Db::name('group_classify')->select();
        $classifyList = \Tree::get_option_tree($classifyList, 0, 'classify_name', 'id', 'parent_id');
        $this->assign('classifyList', $classifyList);

        return $this->fetch();
    }

    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $param = $this->request->param();
        if (!empty($param['classify_name'])) {
            $map['classify_name'] = ['like', '%' . $param['classify_name'] . '%'];//分类名称
        }
        if (!empty($param['group_type'])) {
            $map['group_type'] = $param['group_type'];//类型 1 厂家  2 商家
        }
        if (empty($map)) {
            $map[] = ['exp', '1=1'];
        }

        $count = $this->currentModel->where($map)->count();
        $list = $this->currentModel->where($map)->order('sort asc,id desc')->select();
        $list = collection($list)->append(['parent_text', 'group_type_name', 'group_name'])->toArray();
        $list = \Tree::get_Table_tree($list, 'classify_name', 'id', 'parent_id');

        foreach ($list as $key=>$val) {
            unset($list[$key]['child']);
        }
        $list = array_slice($list, ($param['page'] - 1) * $param['limit'], $param['limit']);
        return ['code'=>0, 'msg'=>'', 'count'=>$count, 'data'=>$list];
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

        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'User');
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

