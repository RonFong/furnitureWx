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

use app\admin\model\Factory;
use app\admin\model\Shop;
use app\admin\model\User;
use think\Request;

class ClientUser extends BaseController
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new User();
    }


    public function index()
    {
        if (!$this->request->has('user_type', 'param', true) ||
            ($this->params['user_type'] != 2 && $this->params['user_type'] != 3))
        {
            $this->error('用户类型参数错误');
        }
        $this->assign('userType', $this->params['user_type']);
        return $this->fetch();
    }


    public function data()
    {
        $key = $this->params['key'];
        $value = trim($this->params['value']);
        $value = empty($value) ? '%' : '%' . $value . '%';
        //用户类型
        $map['type'] = $this->params['type'];
        //根据厂名查找
        if ($this->request->has('factory_name', 'param', true)) {
            $ids = Factory::whereLike('factory_name', '%' . trim($this->params['factory_name']) . '%')->column('id');
            $map['factory_id'] = $ids ? ['in', $ids] : 0;
        }
        //根据商家名查找
        if ($this->request->has('shop_name', 'param', true)) {
            $ids = Shop::whereLike('shop_name', '%' . trim($this->params['shop_name']) . '%')->column('id');
            $map['shop_id'] = $ids ? ['in', $ids] : 0;
        }
        $list = $this->currentModel->where($map)->whereLike($key, $value)->dataTable(['group_name'], ['password']);
        return $list;
    }


    public function edit()
    {
        if ($this->request->has('id', 'param', true)) {
            $userInfo = $this->currentModel->getOne(['id' => $this->params['id']]);
            $this->assign('userInfo', $userInfo);
            $this->params['type'] = $userInfo->type;
        }
        $this->assign('userType', $this->params['type']);
        $this->assign('imgLength', User::IMAGE_MAX_LENGTH);

        //将用户分配到厂、商家
        if ($this->params['type'] == 2) {
            $group = Factory::field('id, factory_name')->select();
        }
        if ($this->params['type'] == 3) {
            $group = Shop::field('id, shop_name')->select();
        }

        $this->assign('group', $group);

        return $this->fetch();
    }


    public function save()
    {
        if (empty($this->params)) {
            $this->error('请填写数用户信息');
        }

        $this->params['upload_image'] = $this->request->file('image');
        $this->params['imageMaxLength'] = User::IMAGE_MAX_LENGTH;
        $validateResult = $this->validate($this->params, 'AdminUser.edit');
        if ($validateResult !== true) {
            $this->error($validateResult);
        }
        $id = $this->currentModel->doSave($this->params);
        if ($id) {
            $this->success('保存成功', "edit?id=$id");
        }
        $this->error('保存失败');

    }


    public function account_exist($id, $account)
    {
        return (new AdminUser())->account_exist($id, $account);
    }

    public function user_name_exist($id, $user_name)
    {
       return (new AdminUser())->user_name_exist($id, $user_name);
    }

    public function unique_number($id, $phone_number)
    {
       return (new AdminUser())->unique_number($id, $phone_number);
    }
}