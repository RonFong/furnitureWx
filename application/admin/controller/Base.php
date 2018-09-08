<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-{2018} http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/6/30 10:25
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\Controller;
use app\admin\model\Menu;
use app\admin\model\UserAdmin;
use think\Session;
use think\Cookie;
use think\Db;
use think\Request;

abstract class Base extends Controller
{
    public $currentModel;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $param = $this->request->param();//获取参数
        /*游客体验，直接进入后台*/
        if (isset($param['id']) && isset($param['visitor']) && $param['id'] == '2' && $param['visitor'] =='1') {
            Session::set('user_info', (new UserAdmin())->getUserInfo(['id'=>$param['id']]));
        }

        /*判断是否已登录*/
        if (empty(user_info('id'))) {
            $this->redirect('Login/index');
        }

        //获取当前链接菜单、父级菜单信息
        $Menu = new Menu();
        $menuCurrent = $Menu->getMenuCurrent();
        $this->assign('menuCurrent', $menuCurrent);

        if (!$this->checkAuth($menuCurrent['id']) && $menuCurrent['id'] != 1) {
            $this->error('您没有权限操作', 'Index/index');
        }

        //将数据列表页显示的行数设置为Cookie缓存
        if (!empty($param['limit']) && $param['limit']<99999 && $param['limit'] != Cookie::get('table_limit')) {
            Cookie::forever('table_limit', $param['limit']);
        }
        $this->assign('table_limit', Cookie::get('table_limit'));

        //非ajax请求，则获取菜单列表、消息提醒
        if (!$this->request->isAjax()) {
            //面包屑
            $breadcrumb = $Menu->get_breadcrumb($menuCurrent['id']);
            $this->assign('breadcrumb', $breadcrumb);

            //获取当前登录用户菜单列表
            $menuList = $Menu->getMenuList();
            $this->assign('menu', $menuList);
        }

    }

    /**
     * 权限验证
     * @param $id string 当前菜单id
     * @return bool
     */
    private function checkAuth($id)
    {
        /*admin账号 或 更改登录者自己的数据，直接通过验证*/
        $id = $this->request->param('id');
        $action = $this->request->action();

        if ($id == (user_info('id')) && ($action == 'edit' || $action == 'changepassword'))
            return true;
        if ((user_info('account') == 'admin') || (user_info('id') == 1))
            return true;

        //获取该账户对应的所有角色权限，并格式化为数组形式
        $auth = model('Role')->whereIn('id', user_info('id'))->column('menu_list');
        $authList = explode(',', implode(',', $auth));

        //获取菜单管理中的菜单id列表
        $menuAll = model('Menu')->column('id');

        /*验证 菜单id列表 是否包含 当前菜单id。包含则继续验证；不包含则直接通过 */
        if (in_array($id, $menuAll)) {
            /*验证 权限菜单id列表 是否包含 当前菜单id。包含则通过*/
            if (in_array($id, array_unique($authList))) {
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * 渲染页面
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 渲染编辑页面
     * @return mixed
     */
    public function edit()
    {
        return $this->fetch();
    }

    /**
     * 删除，并记录日志
     * @param $id
     */
    public function delete($id)
    {
        Db::startTrans();
        try{
            //若存在pid字段，则先删除子部门资料
            $table_name = $this->currentModel->getTableName();
            if (has_field($table_name, 'pid')) {
                var_dump(Db::table($table_name)->whereIn('pid', $id)->fetchSql(true)->select());die;
                $data_child = Db::table($table_name)->whereIn('pid', $id)->select();
                if (!empty($data_child)) {
                    $this->currentModel->whereIn('pid', $id)->delete();
                }
            }

            //删除当前资料，并记录删除日志
            $pk = $this->currentModel->getPk();
            var_dump(Db::table($table_name)->whereIn($pk, $id)->fetchSql(true)->select());die;
            $data = Db::table($table_name)->whereIn($pk, $id)->select();
            if (empty($data)) {
                throw new \Exception('信息不存在');
            }
            $this->currentModel->whereIn($pk, $id)->delete();//删除当前资料
        } catch (\Exception $e) {
            Db::rollback();
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        Db::commit();
        $this->success('删除成功!');
    }

    /**
     * @note上传图片
     * @return \think\response\Json
     */
    public function uploadImg()
    {
        $file = Request::instance()->file('file');
        if (empty($file)) {
            $this->error('上传数据为空');
        } else {
            $info = $file->move(STATIC_PATH . '/img/temp/', time() . rand(100, 999));
            if ($info == false) {
                $this->error($file->getError());
            } else {
                $image = VIEW_STATIC_PATH . '/img/temp/' . $info->getSaveName();
                return json(['code'=>1, 'msg'=>'上传成功', 'data'=>$image], 200, ['Content-Type'=>'text/html']);
//                $this->success('上传成功', null, $image);
            }
        }
    }

    /**
     * @note删除图片
     */
    public function deleteImg()
    {
        $param = $this->request->param();
        if (empty($param['table_name']) || empty($param['field_name']) || empty($param['id']) || empty($param['img_url'])) {
            $this->error("参数错误！");
        }

        if (delete_file($param['img_url'])) {
            $pk = Db::name($param['table_name'])->getPk();
            Db::name($param['table_name'])->where($pk, $param['id'])->update([$param['field_name']=>'']);
        }
        $this->success('操作成功');
    }

    /**
     * 更改排序
     */
    public function changeSort()
    {
        $param = $this->request->param();
        if (empty($param['id']) || empty($param['type'])) {
            $this->error('参数错误');
        }

        try {
            $table_name = $this->currentModel->getTableName();//获取表名
            $list = reset_sort($param['id'], $table_name, $param['type']);//格式化，获取重新排序的数据
            $this->currentModel->saveAll($list);//保存数据
        } catch (\Exception $e) {
            $mag = $this->currentModel->getError() ? $this->currentModel->getError() : $e->getMessage();
            $this->error($mag);
        }

        $this->success('操作成功');
    }

    /**
     * 更新选中数据的状态
     */
    public function updateSelectedState()
    {
        $param = $this->request->param();
        if (empty($param['id']) || !is_array($param['id']) || empty($param['field_name']) || !isset($param['state'])) {
            $this->error('参数不正确');
        }

        $data = [];
        $pk = $this->currentModel->getPk();
        foreach ($param['id'] as $k=>$v) {
            $data[$k][$pk] = $v;
            $data[$k][$param['field_name']] = $param['state'];
        }

        try {
            $this->currentModel->saveAll($data);
        } catch (\Exception $e) {
            $mag = $this->currentModel->getError() ? $this->currentModel->getError() : $e->getMessage();
            $this->error($mag);
        }
        $this->success('更新成功!');
    }

    /**
     * 更新某个字段
     */
    public function updateField()
    {
        $param = $this->request->param();
        $pk = $this->currentModel->getPk();
        if (empty($param[$pk])) {
            $this->error('参数错误');
        }

        try {
            $this->currentModel->save($param);
        } catch (\Exception $e) {
            $msg = $this->currentModel->getError() ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        $this->success('更新成功!');

    }

}