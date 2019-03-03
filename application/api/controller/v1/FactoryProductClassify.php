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

    /**
     * 获取分类
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function getList()
    {
        try {
            if (!array_key_exists('factory_id', $this->data)) {
                exception('factory_id 不能为空');
            }
            $this->result['data'] = $this->currentModel
                ->where('factory_id', $this->data['factory_id'])
                ->field('id, classify_name, sort')
                ->order('sort')
                ->select();
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 删除分类
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function delClassify()
    {
        try {
            if (!array_key_exists('classify_id', $this->data)) {
                exception('classify_id 不能为空');
            }
            $result = $this->currentModel->del($this->data['classify_id']);
            if ($result !== true) {
                exception("此分类下有{$result}个产品，不能删除!");
            }
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
        return json($this->result, 201);
    }
}