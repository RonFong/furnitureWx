<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\User;
use think\Request;
use app\admin\model\Factory as FactoryModel;

/**
 * 厂家列表
 * Class Factory
 * @package app\admin\controller
 */
class Factory extends BaseController
{
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new FactoryModel;
    }

    public function data()
    {
        $map = [];
        if ($this->request->has('factory_name', 'param', true)) {
            $map['factory_name'] = ['like', '%' . trim($this->params['factory_name']) . '%'];
        }
        //根据用户名查找
        if ($this->request->has('user_name', 'param', true)) {
            $ids = User::whereLike('user_name', '%' . trim($this->params['user_name']) . '%')->column('id');
            $map['user_id'] = $ids ? ['in', $ids] : 0;
        }
        return $this->currentModel->where($map)->dataTable(['user_name', 'district']);
    }

    public function edit()
    {
        $factoryData = [];
        if ($this->request->has('id', 'param', true)) {
            $factoryData = $this->currentModel
                ->where('id', $this->params['id'])
                ->field(true)
                ->field('introduce as introduceText')
                ->find();
        }
        //获取可作为新建厂商初始用户的用户
        $users = (new User())->getInitialUser();
        $this->assign('users', $users);

        //添加|编辑时：省下拉列表
        $districtModel = new District();
        $this->assign('provinceList', $districtModel->getArea(0));

        if (!empty($factoryData)) {
            //编辑时需根据地区id获取：地区下拉列表
            $this->assign('cityList', $districtModel->getArea($factoryData['province']));
            $this->assign('districtList', $districtModel->getArea($factoryData['city']));
        }
        $this->assign('factory', $factoryData);
        return $this->fetch();
    }

    public function save()
    {
        if (empty($this->params)) {
            $this->error('请填写厂家信息');
        }

        $this->params['upload_logo'] = $this->request->file('logo');

        $validateResult = $this->validate($this->params, 'Factory.edit');
        if ($validateResult !== true) {
            $this->error($validateResult);
        }

        $row = $this->currentModel->doSave($this->params);
        if ($row) {
            $this->success('保存成功', 'index');
        }
        $this->error('保存失败');
    }

    public function delete($id)
    {
        if ($this->currentModel->where('id', $id)->value('user_id')) {
            $this->error('此厂家已关联厂家用户，不允许删除');
        }
        $row = $this->currentModel->destroy($id);
        if($row === false) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }

    public function factory_name_exist($id, $factory_name)
    {
        if (empty($factory_name)) {
            $row = $this->currentModel->where(['factory_name' => $factory_name])->find();
        } else {
            $row = $this->currentModel->where(['id' => ['neq', $id], 'factory_name' => $factory_name])->find();
        }
        if ($row) {
            return '此厂家名称已存在';
        }
        return true;
    }
}