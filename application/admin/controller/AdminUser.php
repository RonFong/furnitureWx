<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/2/21 
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\User;
use think\Request;

class AdminUser extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new User;
    }

    public function data()
    {
        $key = input('key');
        $value = trim(input('value'));
        $value = empty($value) ? '%' : '%' . $value . '%';
        return $this->currentModel->where('type', 1)->whereLike($key, $value)->dataTable([], ['password']);
    }

    public function edit()
    {
        if ($this->request->has('id', 'param', true)) {
            $userInfo = $this->currentModel->getOne(['id' => input('id')], 'id,account,user_name,image,role_id,status');
            if ($userInfo)
                $this->assign('userInfo', $userInfo);
        }
        $this->assign('imgLength', User::IMAGE_MAX_LENGTH);

        //角色
        $roles = model('role')->field('id, role_name')->select();
        $this->assign('roles', $roles);
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

        $row = $this->currentModel->doSave($this->params);
        if ($row) {
            $this->success('保存成功', 'index');
        }
        $this->error('保存失败');

    }

    /**
     * 密码修改页
     * @return mixed
     */
    public function password()
    {
        return $this->fetch();
    }

    /**
     * 修改密码
     */
    public function updatePassword()
    {
        $params = $this->request->param();
        $checkResult = $this->validate($params, 'AdminUser.updatePassword');
        if(true !== $checkResult){
            $this->error($checkResult);
        }
        $this->success('修改成功');
    }

    /**
     * 远程校验
     * @param $id
     * @param $account
     * @return string
     */
    public function account_exist($id, $account)
    {
        if (empty($id)) {
            $row = $this->currentModel->getOne(['account' => $account]);
        } else {
            $row = $this->currentModel->getOne(['id' => ['neq', $id], 'account' => $account]);
        }
        if ($row) {
            return '此账号名已存在';
        }
        if (strlen($account) < 4 || strlen($account) > 20 ) {
            return '账号长度需在4~20位之间';
        }
        return true;
    }

    public function user_name_exist($id, $user_name)
    {
        if (empty($id)) {
            $row = $this->currentModel->getOne(['user_name' => $user_name]);
        } else {
            $row = $this->currentModel->getOne(['id' => ['neq', $id], 'user_name' => $user_name]);
        }
        if ($row) {
            return '此昵称已存在';
        }
        return true;
    }

    public function unique_number($id, $phone_number)
    {
        if (empty($phone_number)) {
            $row = $this->currentModel->getOne(['phone_number' => $phone_number]);
        } else {
            $row = $this->currentModel->getOne(['id' => ['neq', $id], 'phone_number' => $phone_number]);
        }
        if ($row) {
            return '此号码已存在';
        }
        return true;
    }
}