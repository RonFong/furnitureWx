<?php

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Category as categoryModel;
use app\api\model\GroupClassify;
use app\lib\enum\Response;
use think\Request;

class Category extends BaseController
{
    /**
     * 参数校验统一入口方法
     * @param string $scene 场景
     * @param array $rules 规则
     * Shop constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->currentModel = new categoryModel();
    }

    public function getBusinessCategoryList()
    {
        $this->result['data']['category'] = $this->currentModel->getAllBusinessCategory();
        return json($this->result, 200);
    }

    public function getGroupClassifyList()
    {
        $group_id = user_info('group_id');
        $group_type = user_info('group_type');

        $groupModel = new GroupClassify();
        $result = $groupModel->getClassifyList($group_id,$group_type);
        $this->result['data'] = $result;
        return json($this->result,200);
    }
}