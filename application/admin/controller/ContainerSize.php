<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/5/22 
// +----------------------------------------------------------------------


namespace app\admin\controller;


use think\Request;
use app\admin\model\ContainerSize as ContainerSizeModel;

class ContainerSize extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ContainerSizeModel();
    }

    public function index()
    {
        return $this->fetch();
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDataList()
    {
        $param = $this->request->param();
        $map = [];
        if (!empty($param['name'])) {
            $map['name'] = ['like', '%'.$param['name'].'%'];
        }
        $count = $this->currentModel->where($map)->count();
        $list = $this->currentModel->select();
        foreach ($list as $k => $v) {
            $list[$k]['goods_num'] = (new \app\admin\model\Product())->where('size_ids', 'like', "%{$v['id']}%")->count();
        }
        return ['code'=>0, 'msg'=>'', 'count'=>$count, 'data'=>$list];
    }


    /**
     * 编辑
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $param = $this->request->param();

        if (!empty($param['id'])) {
            $data = $this->currentModel->where('id', $param['id'])->find();
            if (empty($data)) {
                $this->error('信息不存在');
            }
            $data = $data->toArray();
            $this->assign('data', $data);
        }
        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }
        $existInfo = $this->currentModel->where('name', $param['name'])->find();
        if ($existInfo && (empty($param['id']) || $existInfo['id'] != $param['id'])) {
            $this->error('此风格名已存在，请勿重复添加');
        }
        try {
            //保存数据
            $this->currentModel->save($param);
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        $this->success('保存成功！', 'edit?id='.$this->currentModel->id);
    }


}