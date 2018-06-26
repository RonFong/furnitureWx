<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2017/12/31 13:48
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Menu as MenuModel;

class Menu extends BaseController
{

    public function index()
    {
        if ($this->request->has('module', 'param', true)) {
            $this->assign('module', $this->request->param('module'));
        }
        return parent::index();
    }

    /**
     * 新增或者修改
     * @param string $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id = '')
    {
        if(!empty($id)) {
            $all_ids = array_merge([$id], $this->getChildId($id));
            $menuAll = (new MenuModel())
                ->whereNotIn('id',$all_ids)
                ->where('visible',1)
                ->select();
            $menuInfo = (new MenuModel())->get($id);
            $this->assign('MenuInfo',$menuInfo);
            $menuAll = \Tree::get_option_tree($menuAll,$menuInfo['pid'],'menu_name');
        } else {
            $menuAll = (new MenuModel())->all(['visible'=>1]);
            $menuAll = \Tree::get_option_tree($menuAll,$this->request->param('pid',0),'menu_name');
        }
        $this->assign('MenuAll',$menuAll);
        return $this->fetch();
    }

    /**
     * 删除
     * @param $id
     */
    public function delete($id)
    {
        $all_ids = array_merge([$id], $this->getChildId($id));
        $row = (new MenuModel())->destroy($all_ids);
        if($row === false) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }

    /**
     * 保存排序
     */
    public function saveSort()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!empty($data)) {
                $menus = $this->parseMenu($data['menus']);
                (new MenuModel())->saveAll($menus);
                $this->success('保存成功');
            } else {
                $this->error('没有需要保存的节点');
            }
        }
        $this->error('非法请求');
    }

    /**
     * 保存
     */
    public function save()
    {
        $saveData = $this->request->param();
        if(empty($saveData))
        {
            $this->error('没有要保存的数据');
        }
        $row = (new MenuModel())->save($saveData);
        if($row !== false && !empty($saveData['id'])) {
            $allIds = $this->getChildId($saveData['id']);
            foreach ($allIds as $id) {
                $data['id'] = $id;
                $data['is_system'] = $saveData['is_system'];
                $row = (new MenuModel())->update($data);
            }
        }

        if($row !== false) {
            $this->success('保存成功','index');
        } else {
            $this->error((new MenuModel())->getError());
        }
    }

    public function enable_unvisible($id, $visible)
    {
        if($id && $visible == 0) {
            $row = MenuModel::get(['pid'=>$id,'visible'=>0]);
            if($row) {
                return '该节点下存在隐藏节点，不允许变更为隐藏';
            }
        }
        return true;
    }

    /**
     * 递归解析节点
     * @param array $menus 节点数据
     * @param int $pid 上级节点i
     * @return array 解析成可以写入数据库的格式
     */
    private function parseMenu($menus = [], $pid = 0)
    {
        $sort   = 1;
        $result = [];
        foreach ($menus as $menu) {
            $result[] = [
                'id'   => (int)$menu['id'],
                'pid'  => (int)$pid,
                'sort' => $sort,
            ];
            if (isset($menu['children'])) {
                $result = array_merge($result, $this->parseMenu($menu['children'], $menu['id']));
            }
            $sort ++;
        }
        return $result;
    }

    /**
     * 得到所有子节点ID
     * @param $id
     * @return array
     */
    private function getChildId($id)
    {
        $ids = (new MenuModel())->where('pid', $id)->column('id');
        foreach ($ids as $value) {
            $ids = array_merge($ids, $this->getChildId($value));
        }
        return $ids;
    }

}