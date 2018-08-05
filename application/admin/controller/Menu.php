<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Menu as CoreMenu;
use think\Request;

class Menu extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreMenu();//实例化当前模型
    }

    /**
     * 菜单列表页面
     * @return mixed
     */
    public function index()
    {
        $menuListOption = $this->currentModel->getMenuListOption();
        $this->assign('menuListOption', $menuListOption);
        return $this->fetch();
    }
    /**
     * 菜单列表页面 获取数据
     * @throws \think\exception\DbException
     * @return array
     */
    public function getDataList()
    {
        $param = $this->request->param();
        if (!empty($param['pid'])) {
            $map['pid'] = $param['pid'];
        }
        if (!empty($param['menu_name'])) {
            $map['menu_name'] = ['like', '%'.$param['menu_name'].'%'];
        }
        if (!empty($param['url'])) {
            $map['url'] = ['like', '%'.$param['url'].'%'];
        }
        if (empty($map)) {
            $map[] = ['exp', '1=1'];
        }
        $count = $this->currentModel->where($map)->count();
        $list = $this->currentModel->where($map)
            ->field('id,pid,pid as pid_text,menu_name,sort_num,url,description,display as display_text, 
            open_type as open_type_text,menu_name as menu_name_text,is_extend as is_extend_text')
            ->order('sort_num asc')
            ->select();
        $list = \Tree::get_Table_tree($list, 'menu_name_text', 'id');

        foreach ($list as $key=>$val) {
            unset($list[$key]['child']);
        }

        $data = array_slice($list, ($param['page'] - 1) * $param['limit'], $param['limit']);
        return ['code'=>0, 'msg'=>'', 'count'=>$count, 'data'=>$data];
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
        $pid = !empty($data['pid']) ? $data['pid'] : 0;
        $menuListOption = $this->currentModel->getMenuListOption($pid);
        $this->assign('menuListOption', $menuListOption);

        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        if (empty($param['sort_num'])) {
            $count = $this->currentModel->where('pid', $param['pid'])->count();
            $param['sort_num'] = $count+1;
        }
        //验证数据
        $result = $this->validate($param, 'Menu');
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

