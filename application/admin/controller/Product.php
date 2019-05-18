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
use think\Request;


class Product extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ProductModel();
    }

    public function index()
    {
        $classifyOptions = (new GoodsClassify())->getClassifyListOption();
        $this->assign('classifyOptions', $classifyOptions);
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
        $map = [];

        return $map;
    }



}