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

        //风格
        $styleList = Db::table('container_style')->select();
        $this->assign('styleList', $styleList);
        //材质
        $textureList = Db::table('container_texture')->select();
        $this->assign('textureList', $textureList);
        //功能
        $functionList = Db::table('container_function')->select();
        $this->assign('functionList', $functionList);
        //尺寸
        $sizeList = Db::table('container_size')->select();
        $this->assign('sizeList', $sizeList);

        return $this->fetch();
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
            $productData = [
                'id'                => $param['id'],
                'goods_classify_id' => $param['goods_classify_id'],
                'state'             => $param['state'],
                'review_status'     => $param['status']
            ];
            $this->currentModel->save($productData);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('保存成功！', 'reviewEdit?id='.$param['id']);
    }

}