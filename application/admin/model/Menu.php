<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;
use app\common\model\Model;
use think\Db;
use think\Request;

class Menu extends Model
{
    /**
     * 获取“显示”字段，中文名称
     * @param $value
     * @return string
     */
    protected function getDisplayTextAttr($value)
    {
        return $value == '1' ? '是' : '否';
    }

    /**
     * 获取“默认展开”字段，中文名称
     * @param $value
     * @return string
     */
    protected function getIsExtendTextAttr($value)
    {
        return $value == '1' ? '是' : '否';
    }

    /**
     * 获取“打开方式”字段，中文名称
     * @param $value
     * @return string
     */
    protected function getOpenTypeTextAttr($value)
    {
        return $value == '1' ? '当前窗口' : '新窗口';
    }

    /**
     * 获取完整链接地址
     * @param $value
     * @param $data
     * @return string
     */
    protected function getUrlTextAttr($value, $data)
    {
        $res = $value;
        if (empty($value)) {
            $res = '#';
        } elseif (($value != '#') && (strpos($value, 'http') === false)) {
            $res = url($value, $data['params']);
        }
        return $res;
    }


    /**
     * 获取“pid” 对应的中文名称
     * @param $value
     * @return mixed
     */
    protected function getPidTextAttr($value)
    {
        return !empty($value) ? Db::name('menu')->where('id', $value)->value('menu_name') : '顶级';
    }

    protected function setSortNumAttr($value, $data)
    {
        $res = $value;
        if (empty($value) && !empty($data['pid'])) {
            $count = Db::name('menu')->where('pid', $data['pid'])->count();
            $res = $count+1;
        }
        return $res;
    }

    /**
     * 获取面包屑
     * @param $id
     * @param array $res
     * @return string
     * @throws \think\exception\DbException
     */
    public function get_breadcrumb($id, &$res = [])
    {
        $data = $this->where('id', $id)->field('id,pid,menu_name,url as url_text,params')->find();
        $res[$data['id']] = (!empty($data['url_text']) && ($data['url_text'] != '#')) ? "<a href='{$data['url_text']}'>{$data['menu_name']}</a>" : "<a>{$data['menu_name']}</a>";
        if ($data['pid'] != 0) {
            $res[$data['pid']] = $this->get_breadcrumb($data['pid']);
        }
        ksort($res);
        return implode('', $res);
    }

    /**
     * 获取当前登录用户的菜单列表
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\exception\DbException
     */
    public function getMenuListTree()
    {
        $map = [];
        $map['display'] = 1;
        if (user_info('id') != 1) {
            $auth = model('Role')->whereIn('id', user_info('id'))->column('menu_list');
            $map['id'] = ['in', implode(',', $auth)];
        }
        $menuList = $this->where($map)
            ->field('id,pid,menu_name,url as url_text,params,open_type,is_extend')
            ->order('sort_num asc')
            ->select();
        $menuList = \Tree::getTree($menuList, 'id');
        return $menuList;
    }


    /**
     * 获取当前登录用户的菜单列表
     * @param $value string 默认选中id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getMenuListOption($value)
    {
        $map = [];
        $map['display'] = 1;
        if (user_info('id') != 1) {
            $auth = model('Role')->whereIn('id', user_info('id'))->column('menu_list');
            $map['id'] = ['in', implode(',', $auth)];
        }
        $menuList = $this->where($map)
            ->field('id,pid,menu_name,url as url_text,params,open_type,is_extend')
            ->order('sort_num asc')
            ->select();
        $menuList = \Tree::get_option_tree($menuList, $value, 'menu_name');
        return $menuList;
    }

    /**
     * 获取当前链接菜单信息
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\exception\DbException
     */
    public function getMenuCurrent()
    {
        //获取模块名，控制器名，方法名
        $request = Request::instance();
        $controller = $request->module(). '/' .$request->controller(). '/' .$request->action();
        //获取完整链接
        $http_url = $_SERVER['HTTP_HOST'].$request->url();

        //获取当前链接的menu表主键id
        $res = Db::name('menu')
            ->whereLike('url', '%'.$http_url)
            ->whereOr('', 'exp', "url='$controller' AND LOCATE(params,'".http_build_query($request->get())."') > 0")
            ->field('id,id as id_display,pid,pid as pid_display,display,is_extend')
            ->find();
        /*若当前链接菜单为隐藏，则继续查找，页面左侧菜单定位在父级菜单*/
        if ($res['display'] == 2) {
            $res_display = Db::name('menu')
                ->where('id', $res['pid'])
                ->field('id as id_display,pid as pid_display')
                ->find();
            $res = array_merge($res, $res_display);
        }
        return $res;
    }


}