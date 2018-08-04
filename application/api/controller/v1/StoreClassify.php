<?php
namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\lib\sms\SmsApp;
use think\Cache;
use think\Request;

class StoreClassify extends BaseController
{

    /**
     * 参数校验统一入口方法
     * @param string $scene     场景
     * @param array $rules      规则
     * Shop constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->currentModel    = new StoreClassify();
    }

    public function getStoreClassifyList()
    {
        $this->currentModel->storeClassifyList();
    }
}