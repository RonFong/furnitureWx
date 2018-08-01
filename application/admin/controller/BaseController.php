<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2017-12-20 23:25
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\Controller;
use think\Cookie;

abstract class BaseController extends Controller
{
    //当前模型
    public $currentModel;

    protected function _initialize()
    {
        $module = cookie('module');
        if(empty($module))
        {
            $module = session('module') ?: 1;
        }
        $menu = model('Menu');
        //得到当前操作地址
        $fullUrl = $this->request->module().'/'.$this->request->controller().'/'.$this->request->action();
        //得到当前控制器信息
        $params = $this->request->get();
        if($params) {
            $currentMenu = $menu->where(['controller'=>$fullUrl])->whereExp('','LOCATE("'.http_build_query($params).'",params) > 0')->value('id');
            $pidMenu = $menu->where(['controller'=>$fullUrl])->whereExp('','LOCATE("'.http_build_query($params).'",params) > 0')->value('pid');
        }

        $currentMenu = isset($currentMenu) && !empty($currentMenu) ? $currentMenu : $menu->where(['controller'=>$fullUrl])->value('id');
        $pidMenu = isset($pidMenu) && !empty($pidMenu) ? $pidMenu : $menu->where(['controller'=>$fullUrl])->value('pid');

        $this->assign('currentMenu',$currentMenu);
        //得到当前父控制器信息
        $this->assign('pidMenu',$pidMenu);
        $auth = db('role')->whereIn('id',user_info('role_id'))->column('menu_list');
        $role = [];
        foreach ($auth as $item) {
            $role = array_merge($role,explode(',',$item));
        }
        $this->assign('auth',array_unique($role));
        if(!$this->checkAuth($currentMenu,array_unique($role))) {
            $this->error('无操作权限');
        }
        //得到权限菜单列表
        if(user_info('account') != 'admin') {
            $userMenu = db('role')->where('id',user_info('role_id'))->value('menu_list');
            $menu->where('is_system', 0)
                ->where('visible', 1)
                ->whereIn('id', $userMenu)
                ->where('module', $module);
        } else {
            $menu->where('visible',1)
                ->where('module',$module);
        }
        $menuList = $menu->field('params',true)->field('params as params_array')->order('sort')->select();
        $menuList = \Tree::getTree($menuList);
        $this->assign('menus',$menuList);
        //得到权限菜单列表
        $allMenu = $menu->order('sort')->field('params',true)->field('params as params_array')->select();
        $allMenu = \Tree::getTree($allMenu);
        $this->assign('allMenu',$allMenu);
    }

    private function checkAuth($currentMenu,$auth)
    {
        $menu = model('Menu');
        $allMenu = $menu->column('id');
        if(user_info('account') == 'admin') return true;
        if(in_array($currentMenu,$allMenu)) {
            if(in_array($currentMenu,$auth)) {
                return true;
            }
            return false;
        }
        return true;
    }

    public function index()
    {
        return $this->fetch();
    }

    /**
     *展示数据
     */
    public function data()
    {
        return [
            "draw" => intval($this->request->param('draw')),
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0
        ];
    }
    /**
     *编辑
     */
    public function edit()
    {
        return $this->fetch();
    }

    /**
     * 删除
     * @param $id
     */
    public function delete($id)
    {
        $row = $this->currentModel->destroy($id);
        if($row === false) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }

    public function changeModule($module)
    {
        session('module',$module);
        Cookie::forever('module',$module);
        $this->redirect('Index/index');
    }


}
