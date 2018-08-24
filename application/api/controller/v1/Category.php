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
        $type = $this->data['type'] ?? 1;
        $id = $this->data['id'] ?? '';
        $group_id = user_info('group_id');
        $group_type = user_info('type');

        $groupModel = new GroupClassify();
        $result = $groupModel->getClassifyList($type,$id,$group_id,$group_type);
        $this->result['data'] = $result;
        return json($this->result,200);
    }

    public function AddGroupClassify()
    {
        $save_data = $this->data;
        $save_data['group_id'] = user_info('group_id');
        $save_data['group_type'] = user_info('type');
        $groupModel = new GroupClassify();
        $update = false;
        if(isset($save_data['id'])){
            $update = true;
        }
        $res = $groupModel->isUpdate($update)->save($save_data);
        if(!$res){
            $this->result['state'] = 0;
            $this->result['msg'] = '保存分类失败';
            return json($this->result, 200);
        }
        return json($this->result, 200);
    }

    public function getSortGroupClassify()
    {
        $sort_data = $this->data;
        $group_id = user_info('group_id');
        $group_type = user_info('type');
        $groupModel = new GroupClassify();
        $res = $groupModel->sortGroupClassify($sort_data,$group_id,$group_type);
        if(!$res){
            $this->result['state'] = 0;
            $this->result['msg'] = '分类排序失败';
            return json($this->result, 200);
        }
        return json($this->result, 200);
    }

    public function delGroupClassify()
    {
        $id = $this->data['id'] ?? '' ;
        $parent = $this->data['id'] ?? false;
        $groupModel = new GroupClassify();
        if($parent){
            $delRes = $groupModel->where('id',$id)->whereOr('parent_id',$id)->delete();
        }else{
            $delRes = $groupModel->where('id',$id)->delete();
        }
        if(!$delRes){
            $this->result['state'] = 0;
            $this->result['msg'] = '分类删除失败';
            return json($this->result, 200);
        }
        return json($this->result, 200);
    }
}