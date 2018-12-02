<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\HomeContent as homeContentModel;
use app\common\validate\HomeContent as homeContentValidate;
use app\lib\enum\Response;
use think\Request;

class HomeContent extends BaseController
{

    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->currentModel    = new homeContentModel();
        $this->currentValidate = new homeContentValidate();
    }

    /**
     * 创建图文
     * @throws \app\lib\exception\BaseException
     */
    public function create()
    {
        try {
        $this->currentValidate->goCheck('create');
        $this->result['data'] = $this->currentModel->createData($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        if (!$this->result['data']) {
            $this->response->error(Response::UNKNOWN_ERROR);
        }
        return json($this->result, 201);
    }


    /**
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function update()
    {
        try {
            $this->currentValidate->goCheck('update');
            $result = $this->currentModel->updateData($this->data);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        if (!$result) {
            $this->response->error(Response::UNKNOWN_ERROR);
        }
        return json($this->result, 200);
    }

    /**
     * 获取首页图文详情
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function details()
    {
        if (user_info('type') == 3 || user_info('group_id') == 0) {
            return json(['state' => 0, 'msg' => '无数据'], 400);
        }
        $this->result['data'] = $this->currentModel->details();
        if (!$this->result['data']) {
            return json(['state' => 0, 'msg' => '无数据'], 400);
        }
        return json($this->result, 200);
    }
}