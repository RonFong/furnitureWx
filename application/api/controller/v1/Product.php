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
     * @throws \app\lib\exception\BaseException
     */
    public function create()
    {
        $this->currentValidate->goCheck('create');
        $this->result['data']['id'] = $this->currentModel->saveData($this->data);
        return json($this->result, 200);
    }

    /**
     * 修改产品信息
     * @throws \app\lib\exception\BaseException
     */
    public function update()
    {
        $this->currentValidate->goCheck('update');
        $this->result['data']['id'] = $this->currentModel->saveData($this->data);
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
        try {
            if (!array_key_exists('classify_id', $this->data)) {
                exception('classify_id 不能为空');
            }
            $this->result['data']['list'] = $this->currentModel->getListByClassify($this->data['classify_id']);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 200);
    }
}