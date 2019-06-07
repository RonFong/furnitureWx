<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/5 
// +----------------------------------------------------------------------


namespace app\admin\controller;


use think\Request;
use app\common\model\GoodsAttr as GoodsAttrModel;

/**
 * 属性类别
 * Class GoodsAttr
 * @package app\admin\controller
 */
class GoodsAttr extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new GoodsAttrModel();
    }

    public function index()
    {
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
        if (!empty($param['classify_name'])) {
            $map['classify_name'] = ['like', '%'.$param['classify_name'].'%'];
        }
        if (!empty($param['attr_code'])) {
            $map['attr_code'] = ['like', '%'.$param['attr_code'].'%'];
        }
        $list = $this->currentModel->where($map)
            ->field(true)
            ->order('sort_num asc')
            ->layTable(['val_num']);
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
            $this->assign('attrData', $attrData);
        }

        return $this->fetch();
    }


    public function saveAttr()
    {
        $param = $this->request->param();

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }
        //验证数据
        $result = $this->validate($param, 'GoodsAttr');
        if ($result !== true) {
            $this->error($result);
        }
        try {
            //保存数据
            if (empty($param['id'])) {
                $param['sort_num'] = $this->currentModel->count() + 1;
                $param['attr_code'] = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 6);
            }
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
        $enum = (new \app\common\model\GoodsAttrVal())->where('attr_id', $id)->select();
        if ($enum) {
            $this->error('此类别下有属性枚举值，不能删除');
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