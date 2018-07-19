<?php

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Category as categoryModel;
use app\lib\enum\Response;
use think\Request;

class Category extends BaseController
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
        $this->currentModel    = new categoryModel();
    }

    public function getCategoryList()
    {
        $this->result['data']['category'] = $this->currentModel->getAllCategory();
        return json($this->result,200);
    }
}