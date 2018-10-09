<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/10/8 
// +----------------------------------------------------------------------


namespace app\admin\controller;


use think\Request;
use app\admin\model\GroupClassify;

class FactoryClassify extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new GroupClassify();
    }

    public function index()
    {
        $groupId = $this->request->param('group_id') ?? '';
        $this->assign('groupId', $groupId);
        return $this->fetch();
    }

    public function getDataList()
    {
        $param = $this->request->param();
        $map = ['group_type' => 1];
        if (!empty($param['classify_name'])) {
            $map['classify_name'] = ['like', "%{$param['classify_name']}%"];
        }
        if (!empty($param['group_id'])) {
            $map['group_id'] = $param['group_id'];
        }

        return $this->currentModel->where($map)->order('group_id desc')->layTable(['group_name']);
    }

}