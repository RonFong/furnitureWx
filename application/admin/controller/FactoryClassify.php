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
use app\admin\model\FactoryClassify as FactoryClassifyModel;

class FactoryClassify extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new FactoryClassifyModel();
    }

    public function index()
    {
        return $this->fetch();
    }

    public function getDataList()
    {
        $param = $this->request->param();
        $map = [];
        if (!empty($param['classify_name'])) {
            $map['classify_name'] = ['like', "%{$param['classify_name']}%"];
        }

        return $this->currentModel->where($map)->order('factory_id desc')->layTable();
    }

}