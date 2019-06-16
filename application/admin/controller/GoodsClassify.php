<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/10/10 
// +----------------------------------------------------------------------


namespace app\admin\controller;


use app\common\model\GoodsClassifyAttr;
use think\Db;
use think\Request;
use \app\admin\model\GoodsClassify as GoodsClassifyModel;

class GoodsClassify extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new GoodsClassifyModel();
    }

    public function index()
    {
        $classifyListOption = $this->currentModel->getClassifyListOption();
        $this->assign('classifyListOption', $classifyListOption);
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
        $map = [];
        if (!empty($param['pid'])) {
            $map['pid'] = $param['pid'];
        }
        if (!empty($param['classify_name'])) {
            $map['classify_name'] = ['like', '%'.$param['classify_name'].'%'];
        }
        $count = $this->currentModel->where($map)->count();
        $list = $this->currentModel->where($map)
            ->field(true)
            ->field('pid as pid_name, id as goods_num')
            ->order('sort_num asc')
            ->select();
        $list = \Tree::get_Table_tree($list, 'classify_name', 'id');
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
        $data = ['styles' =>[], 'functions'=>[], 'textures'=>[], 'sizes'=>[]];
        $data['attrList'] = [];
        if (!empty($param['id'])) {
            $data = $this->currentModel->where('id', $param['id'])->find()->toArray();
            if (empty($data)) {
                $this->error('信息不存在');
            }
            $attrList = (new GoodsClassifyAttr())
                ->alias('a')
                ->join('goods_attr_val b', 'a.attr_val_id = b.id')
                ->field('b.id, b.enum_name, b.attr_id')
                ->where('a.goods_classify_id', $param['id'])
                ->select();
            $attrs = Db::table('goods_attr')->field('id, attr_name')->select();
            foreach ($attrs as $k => $v) {
                $v['enum_list'] = [];
                foreach ($attrList as $kk => $vv) {
                    if ($vv['attr_id'] == $v['id']) {
                        array_push($v['enum_list'], $vv->toArray());
                    }
                }
                $data['attrList'][$v['id']] = $v;
            }
        }
        $this->assign('data', $data);
        $pid = $data['pid'] ?? 0;
        $list = $this->currentModel->field(true)->select();
        $classifyListOption = \Tree::get_option_tree($list, $pid, 'classify_name', 'id');
        $this->assign('classifyListOption', $classifyListOption);

        return $this->fetch();
    }


    /**
     * 获取属性
     * @param $currentTable  string   当前表
     * @param $joinTable  string   属性表名
     * @param $field   string  属性关联字段
     * @param $id   int    分了id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getAttr($currentTable, $joinTable, $field, $id)
    {
        return Db::table($currentTable)
            ->alias('a')
            ->join("$joinTable b", "a.$field = b.id")
            ->where('a.goods_classify_id', $id)
            ->field('b.id, b.name')
            ->select();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }
        //验证数据
        $result = $this->validate($param, 'GoodsClassify');
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
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $goods = (new \app\admin\model\Product())->where('goods_classify_id', $id)->select();
        if ($goods) {
            $this->error('此分类下有商品，不能删除');
        }
        $this->currentModel->where('id', $id)->delete();
        $this->success('删除成功');
    }

    /**
     * 删除分类的属性
     * @param $id   分类id
     * @param $attr  属性名
     * @param $attrId   属性id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delAttr($id, $attr_val_id)
    {
        Db::table('goods_classify_attr')->where(['goods_classify_id' => $id, 'attr_val_id' => $attr_val_id])->delete();
        $this->success('删除成功');
    }

    /**
     * 获取属性
     * @param $id
     * @param $attr
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttrs($id, $attr_id)
    {
        $attrList = Db::table('goods_attr_val')
            ->alias('a')
            ->join('goods_classify_attr b', "a.id = b.attr_val_id and goods_classify_id = $id", 'LEFT')
            ->field('a.id, a.enum_name, a.tag, ifnull(b.id, 0) as checked')
            ->where('a.attr_id', $attr_id)
            ->select();
        $data = [];
        if ($attrList) {
            $data = ['无标签' => []];
            foreach ($attrList as $k => $v) {
                if (empty($v['tag'])) {
                    array_push($data['无标签'], $v);
                } else {
                    if (isset($data[$v['tag']])) {
                        array_push($data[$v['tag']], $v);
                    } else {
                        $data[$v['tag']] = [$v];
                    }
                }
            }
        }
        $this->success('success', '', $data);
    }

    /**
     * 更新分类的属性
     */
    public function saveAttr()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $attrId = $param['attr_id'];
        unset($param['id'], $param['attr_id']);
        Db::startTrans();
        try {
            $existAttrIds = Db::table('goods_classify_attr')
                ->alias('a')
                ->join('goods_attr_val b', 'a.attr_val_id = b.id')
                ->join('goods_attr c', 'b.attr_id = c.id')
                ->where('a.goods_classify_id', $id)
                ->where('c.id', $attrId)
                ->column('a.id');
            $deledAttr = Db::table('goods_classify_attr')
                ->alias('a')
                ->join('goods_attr_val b', 'a.attr_val_id = b.id', 'LEFT')
                ->where('a.goods_classify_id', $id)
                ->where('b.id is null')
                ->column('a.id');
            Db::table('goods_classify_attr')->where('id', 'in', implode(',', array_merge($existAttrIds, $deledAttr)))->delete();
            $saveData = [];
            foreach ($param as $k => $v) {
                $saveData[$k]['attr_val_id'] = $v;
                $saveData[$k]['goods_classify_id'] = $id;
            }
            Db::table('goods_classify_attr')->insertAll($saveData);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('保存成功');
    }
}