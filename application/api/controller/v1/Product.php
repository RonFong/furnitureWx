<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/27 
// +----------------------------------------------------------------------


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\common\model\ProductReviewStatus;
use think\Db;
use think\Request;
use app\api\model\Product as ProductModel;
use app\common\validate\Product as ProductValidate;

/**
 * 厂家产品
 * Class Product
 * @package app\api\controller\v1
 */
class Product extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ProductModel();
        $this->currentValidate = new ProductValidate();
    }

    /**
     * 发布产品
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        $this->currentValidate->goCheck('create');
        $this->result['data']['id'] = $this->currentModel->saveData($this->data);
        //写入审核进度
        (new ProductReviewStatus())->write($this->result['data']['id'], 0);

        return json($this->result, 200);
    }

    /**
     * 修改产品信息
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $this->currentValidate->goCheck('update');
        $this->result['data']['id'] = $this->currentModel->saveData($this->data);
        //写入审核进度
        (new ProductReviewStatus())->write($this->result['data']['id'], 0);
        return json($this->result, 200);
    }

    /**
     * 更改产品上下架状态
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function shelvesStatus()
    {
        $this->currentValidate->goCheck('shelvesStatus');
        $this->currentModel->where('id', $this->data['product_id'])->update(['is_on_shelves' => $this->data['status']]);
        return json($this->result, 200);
    }

    /**
     * 产品详情
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function info()
    {
        $this->currentValidate->goCheck('info');
        try {
            $this->result['data'] = $this->currentModel->info($this->data['product_id']);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 获取零售价计算比例
     * @return mixed
     */
    public function retailPriceRatio()
    {
        $this->result['data']['ratio'] = config('system.price_ratio');
        return json($this->result, 200);
    }

    /**
     * 按分类获取产品列表
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function getListByClassify()
    {
        $this->currentValidate->goCheck('getListByClassify');
        try {
            $this->result['data']['list'] = $this->currentModel->getListByClassify($this->data['classify_id'], $this->data['page'], $this->data['row']);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 删除产品
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function delProduct()
    {
        $this->currentValidate->goCheck('delProduct');
        try {
            $result = $this->currentModel->del($this->data['product_id']);
            if ($result !== true) {
                exception($result);
            }
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 产品移动到其他分类
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function changeClassify()
    {
        $this->currentValidate->goCheck('changeClassify');
        try {
            $this->currentModel->changeClassify($this->data['product_id'], $this->data['classify_id']);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 201);
    }

    /**
     * 更改产品排序
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function sort()
    {
        $this->currentValidate->goCheck('sort');
        try {
            $this->currentModel->sort($this->data['product_id'], $this->data['sort_action']);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 201);
    }

    /**
     * 产品编辑页参数
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function attr()
    {
        $this->result['data']['brand'] = Db::table('product')->where('factory_id', user_info('group_id'))->order('id desc')->value('brand');
        $this->result['data']['ratio'] = config('system.price_ratio');
        $this->result['data']['classifyList'] = Db::table('factory_product_classify')
            ->where('factory_id', user_info('group_id'))
            ->field('id, classify_name, sort')
            ->order('sort')
            ->select();
        return json($this->result, 200);
    }
}