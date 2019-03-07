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
use app\api\model\FactoryIntro;
use app\api\service\Popularity;
use think\Request;
use app\api\model\Factory as FactoryModel;
use app\common\validate\Factory as FactoryValidate;
use app\common\validate\FactoryIntro as FactoryIntroValidate;

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
            $this->result['data']['id'] = $this->currentModel->saveInfo($this->data);
            return json($this->result, 201);
        } catch (\Exception $e) {
            $this->currentValidate->error($e);
        }
    }

    /**
     * 更改厂家信息厂家
     * @throws \app\lib\exception\BaseException
     */
    public function update()
    {
        $this->currentValidate->goCheck('update');
        try {
            $this->result['data']['id'] = $this->currentModel->saveInfo($this->data);
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

    /**
     * 门店首页数据
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function homePage()
    {
        if (!isset($this->data['factoryId']) && user_info('type') !== 1) {
            $this->result['state'] = 0;
            $this->result['msg'] = '非厂家用户';
            return json($this->result, 403);
        }
        try {
            $factoryId = $this->data['factoryId'] ?? user_info('group_id');
            $this->result['data'] = $this->currentModel->homePageData($factoryId);
            //增加人气值
            Popularity::increase($factoryId, 1);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * 获取厂家信息
     * @return \think\response\Json
     */
    public function info()
    {
        try {
            if (!array_key_exists('factory_id', $this->data)) {
                exception('factory_id 不能为空');
            }
            $this->result['data'] = $this->currentModel
                ->where('id', $this->data['factory_id'])
                ->find();
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }


    /**
     * 写入简介
     * @return \think\response\Json
     */
    public function createIntro()
    {
        (new FactoryIntroValidate())->goCheck('createIntro');
        try {
            (new FactoryIntro())->createData($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 201);
    }

    /**
     * 写入简介
     * @return \think\response\Json
     */
    public function updateIntro()
    {
        (new FactoryIntroValidate())->goCheck('updateIntro');
        try {
            (new FactoryIntro())->updateData($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 201);
    }

    /**
     * 获取厂家简介
     * @return \think\response\Json
     */
    public function introInfo()
    {
        (new FactoryIntroValidate())->goCheck('introInfo');
        try {
            $this->result['data'] = (new FactoryIntro())->introInfo($this->data['factory_id']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }


    /**
     * 获取联系信息
     * @return \think\response\Json
     */
    public function contactInfo()
    {
        $this->currentValidate->goCheck('contactInfo');
        try {
            $this->result['data'] = $this->currentModel->contactInfo($this->data['factory_id']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }

}