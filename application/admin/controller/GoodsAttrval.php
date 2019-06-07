<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/5
// +----------------------------------------------------------------------


namespace app\admin\controller;


use think\Cache;
use think\Request;
use app\common\model\GoodsAttrVal as GoodsAttrValModel;

/**
 * 属性类别
 * Class GoodsAttr
 * @package app\admin\controller
 */
class GoodsAttrval extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new GoodsAttrValModel();
    }

    public function index()
    {
        $attrList = (new \app\common\model\GoodsAttr())->order('sort_num')->select();
        $this->assign('attrList', $attrList);

        if ($this->request->has('attr_id', 'param', true)) {
            Cache::set('search_attr_id', $this->request->param('attr_id'));
        }
        return $this->fetch();
    }

    /**
     * @return array
     */
    public function getDataList()
    {
        $param = $this->request->param();
        $map = [];
        if (!empty($param['enum_name'])) {
            $map['enum_name'] = ['like', '%'.trim($param['enum_name']).'%'];
        }
        if (!empty($param['tag'])) {
            $map['tag'] = ['like', '%'.trim($param['tag']).'%'];
        }
        if (!empty($param['attr_id'])) {
            $map['attr_id'] = $param['attr_id'];
        }
        $attrId = Cache::pull('search_attr_id');
        if ($attrId) {
            $map['attr_id'] = $attrId;
        }
        $list = $this->currentModel->where($map)
            ->field(true)
            ->order('attr_id, sort_num asc')
            ->layTable(['product_num']);
        return $list;
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
            $attrData = $this->currentModel->where('id', $param['id'])->find();
            $this->assign('data', $attrData);
        }
        $attrList = (new \app\common\model\GoodsAttr())->order('sort_num')->select();
        $this->assign('attrList', $attrList);

        return $this->fetch();
    }


    public function save()
    {
        $param = $this->request->param();

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }
        //验证数据
        $result = $this->validate($param, 'GoodsAttrVal');
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
     * 获取属性
     * @param $id
     * @param $attr
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttrs($id, $attr)
    {
        $data = Db::table('container_' . $attr)
            ->alias('a')
            ->join("goods_{$attr} b", "b.{$attr}_id = a.id and goods_classify_id = {$id}", 'LEFT')
            ->field('a.id, a.name, ifnull(b.id, 0) as selected')
            ->select();
        $this->success('success', '', $data);
    }
}