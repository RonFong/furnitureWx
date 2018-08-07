<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Role as CoreRole;
use think\Db;
use think\Request;

class Role extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreRole();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $param = $this->request->param();
        if (!empty($param['role_name'])) {
            $map['role_name'] = ['like', '%'.$param['role_name'].'%'];//角色名称
        }
        if (empty($map)) {
            $map[] = ['exp', '1=1'];
        }

        return $this->currentModel->where($map)->order('sort_num')->layTable();
    }

    /**
     * 编辑
     * @return mixed
     */
    public function edit()
    {
        $param = $this->request->param();

        if (!empty($param['id'])) {
            $data = $this->currentModel->where('id', $param['id'])->field('id,role_name,describe,menu_list')->find();
            if (empty($data)) {
                $this->error('信息不存在');
            }
            $data = $data->toArray();
            $this->assign('data', $data);
        }

        return $this->fetch();
    }

    /**
     * 获取菜单列表数据
     * @param $role_id
     * @return array
     */
    public function getMenuList($role_id)
    {
        $list = Db::name('menu')->field('id,pid,menu_name,sort_num,url,description')->order('sort_num asc')->select();
        $auth = Db::name('user_role')->where('id', $role_id)->value('menu_list');
        $auth_arr = explode(',', $auth);
        $list = \Tree::get_Table_tree($list, 'menu_name', 'id');
        foreach ($list as $k=>$v) {
            $list[$k]['pid_text'] = !empty($v['pid']) ? Db::name('menu')->where('id', $v['pid'])->value('menu_name') : '顶级';
            $list[$k]['LAY_CHECKED'] = in_array($v['id'], $auth_arr) || $v['id'] == 1 ? true : false;
            unset($list[$k]['child']);
        }
        return ['code'=>0, 'msg'=>'获取成功', 'count'=>0, 'data'=>$list];
    }

    /**
     * 保存数据
     */
    public function save()
    {
        $param = $this->request->param();//获取请求数据
        //验证数据
        $result = $this->validate($param, 'UserRole');
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

    /**
     * 编辑字段
     */
    public function updateField()
    {
        $param = $this->request->param();
        if (empty($param['id'])) {
            $this->error('角色id不能为空');
        }

        //验证数据
        $result = $this->validate($param, 'UserRole.updateField');
        if ($result !== true) {
            $this->error($result);
        }

        try {
            $this->currentModel->save($param);
        } catch (\Exception $e) {
            $this->error($this->currentModel->getError() ? $this->currentModel->getError() : $e->getMessage());
        }
        $this->success('更新成功!', 'index');
    }

}

