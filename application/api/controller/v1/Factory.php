<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/19 
// +----------------------------------------------------------------------


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use think\Request;
use app\api\model\Factory as FactoryModel;
use app\common\validate\Factory as FactoryValidate;

/**
 * 厂家
 * Class Factory
 * @package app\api\controller\v1
 */
class Factory extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new FactoryModel();
        $this->currentValidate = new FactoryValidate();
    }


    /**
     * 创建厂家
     * @throws \app\lib\exception\BaseException
     */
    public function create()
    {
        $this->currentValidate->goCheck('create');
        try {
            $this->result['data']['id'] = $this->currentModel->createFactory($this->data);
            return json($this->result, 201);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
    }


    /**
     * 填写工厂信息
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function supplementInfo()
    {
        $this->currentValidate->goCheck('supplementInfo');
        try {
            $this->currentModel->supplementInfo($this->data);
            return json($this->result, 201);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }

    }

}