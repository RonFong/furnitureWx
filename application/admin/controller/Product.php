<?php
/**
 * Created by PhpStorm.
 * User: 50xiuer
 * Date: 2019/5/3
 * Time: 18:28
 */

namespace app\admin\controller;

use app\admin\model\Product as ProductModel;
use app\admin\model\GoodsClassify;
use app\common\model\ProductReviewStatus;
use think\Db;
use think\Request;


class Product extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ProductModel();
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $list = (new GoodsClassify())->select();
        $classifyListOption = \Tree::get_option_tree($list, 0, 'classify_name', 'id');
        $this->assign('classifyListOption', $classifyListOption);
        return $this->fetch();
    }


    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $map = $this->getDataListMap();
        return $this->currentModel
            ->where($map)
            ->order('id desc')
            ->layTable(['factory_name', 'goods_classify_name', 'classify_name', 'image']);
    }

    public function getDataListMap()
    {
        $param = $this->request->param();
        $map = [];
        if ($this->request->has('review_status', 'param', true)) {
            $map['review_status'] = $param['review_status'];
        }
        if ($this->request->has('state', 'param', true)) {
            $map['state'] = $param['state'];
        }
        if ($this->request->has('name', 'param', true)) {
            $map['name'] = ['like', "%{$param['name']}%"];
        }
        if ($this->request->has('goods_classify_id', 'param', true)) {
            $map['goods_classify_id'] = $param['goods_classify_id'];
        }
        return $map;
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $id = $this->request->param('id');
        $data = (new \app\api\model\Product())->info($id, 0 , true);
        $data = json_decode(json_encode($data), true, JSON_UNESCAPED_UNICODE);
        $data['factory_name'] = (new \app\admin\model\Factory())->where('id', $data['factory_id'])->value('factory_name');
        $this->assign('data', $data);

        return $this->fetch();
    }

    /**
     * 获取分类选项
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ajaxClassify()
    {
        $pid = $this->request->param('pid');
        return (new GoodsClassify())->where('pid', $pid)->select();
    }


    public function save()
    {
        $param = $this->request->param();//获取请求数据
        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'Product');
        if ($result !== true) {
            $this->error($result);
        }

        try {
            //保存数据
            $this->currentModel->save($param);
            if (!empty($param['id'])) {
                $reviewData = [
                    'product_id'    => $param['id'],
                    'status'        => $param['status'],
                    'remark'        => $param['remark']
                ];
                (new ProductReviewStatus())->save($reviewData);
            }
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        $this->success('保存成功！', 'edit?id='.$this->currentModel->id);
    }

    /**
     * 编辑审核信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function reviewEdit()
    {
        $id = $this->request->param('id');
        $data = \app\admin\model\Product::get($id);
        $review = Db::table('product_review_status')->where('product_id', $id)->order('id desc')->limit(0, 1)->find();
        $this->assign('review', $review);

        $classifyModel = new GoodsClassify();
        $classifyInfo = $classifyModel->where('id', $data['goods_classify_id'])->find();  //当前商城分类
        $classifyParent = $classifyModel->where('id', $classifyInfo['pid'])->find();      //当前分类的父级分类

        $data['classify_name'] = $classifyInfo['classify_name'];
        $this->assign('classifyParent', $classifyParent);

        $classifyCurrentList = $classifyModel->where('pid', $classifyInfo['pid'])->select();      //当前父级下的子分类
        $this->assign('classifyCurrentList', $classifyCurrentList);

        $this->assign('data', $data);
        $classifyParentList = (new GoodsClassify())->where('pid', 1)->select();    // 二级分类
        $this->assign('classifyParentList', $classifyParentList);

        //当前产品的商城属性， json 转  , 拼接的字符串
        $attrIds = json_decode($data['attr_ids'], true) ?? [];
        $attrIdsStr = '';
        foreach ($attrIds as $k => $v) {
            $attrIdsStr .= $v . ',';
        }
        $attrEnum = Db::table('goods_attr_val')
            ->field('id, enum_name, attr_id')
            ->where('id', 'in', $attrIdsStr)
            ->select();

        $attrs = Db::table('goods_attr')->field('id, attr_name')->select();
        $attrList = [];
        foreach ($attrs as $k => $v) {
            $v['enum_list'] = [];
            foreach ($attrEnum as $kk => $vv) {
                if ($vv['attr_id'] == $v['id']) {
                    array_push($v['enum_list'], $vv);
                }
            }
            $attrList[$v['id']] = $v;
        }
        $this->assign('attrList', $attrList);

        return $this->fetch();
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
        Db::table('product_attr')->where(['product_id' => $id, 'attr_val_id' => $attr_val_id])->delete();
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
    public function getAttrs($id, $attr_id, $goods_classify_id)
    {
        $attrList = Db::table('goods_classify_attr')
            ->alias('a')
            ->join('goods_attr_val b', 'a.id = b. goods_classify_id')
            ->field('b.id, b.enum_name, b.tag')
            ->where(['a.id' => $goods_classify_id, 'b.attr_id' => $attr_id])
            ->select();
        $attrIds = Db::table('product')->where('id', $id)->value('attr_ids');
        foreach ($attrList as $k => $v) {
            $isChecked = 0;
            if ($attrIds) {
                $tmpArr = json_decode($attrIds, true);
                foreach ($tmpArr as $kk => $vv) {
                    if (in_array($v['id'], explode(',', $vv)) !== false) {
                        $isChecked = 1;
                        continue;
                    }
                }
            }
            $attrList[$k]['checked'] = $isChecked;
        }

        $data = [];
        if ($attrList) {
            $data = ['无标签' =>[]];
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
        $attrCode = Db::table('goods_attr')->where('id', $param['attr_id'])->value('attr_code');
        unset($param['id'], $param['attr_id']);

        $data = Db::table('product')->where('id', $id)->value('attr_ids') ?? '{}';
        $data = json_decode($data, true);
        $data[$attrCode] = str_replace(' ', '', implode(',', $param));
        Db::table('product')->where('id', $id)->update(['attr_ids' => json_encode($data)]);

        $this->success('保存成功');
    }

    /**
     * 保存审核结果
     */
    public function reviewSave()
    {
        $param = $this->request->param();//获取请求数据
        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }
        //验证数据
        $result = $this->validate($param, 'Product');
        if ($result !== true) {
            $this->error($result);
        }
        try {
            $reviewData = [
                'product_id'    => $param['id'],
                'status'        => $param['status'],
                'remark'        => $param['remark']
            ];
            (new ProductReviewStatus())->save($reviewData);
            $param['review_status'] = $param['status'];
            $this->currentModel->save($param);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('保存成功！', 'reviewEdit?id='.$param['id']);
    }

}