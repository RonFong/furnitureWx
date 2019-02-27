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
}