<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/20 
// +----------------------------------------------------------------------


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use think\Request;
use app\api\model\FactoryProductClassify as FactoryProductClassifyModel;
use app\common\validate\FactoryProductClassify as FactoryProductClassifyValidate;


class FactoryProductClassify extends BaseController
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new FactoryProductClassifyModel();
        $this->currentValidate = new FactoryProductClassifyValidate();
    }


    /**
     * 新建分类
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function create()
    {
        $this->currentValidate->goCheck('create');
        try {
            $this->data['factory_id'] = user_info('group_id');
            $this->data['sort'] = $this->currentModel->where('factory_id', user_info('group_id'))->count();
            $this->currentModel->save($this->data);
            $this->result['data']['id'] = $this->currentModel->id;
            return json($this->result, 201);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
    }

    /**
     * 修改分类信息
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function edit()
    {
        $this->currentValidate->goCheck('edit');
        try {
            $this->currentModel->save($this->data);
            return json($this->result, 201);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
    }
}