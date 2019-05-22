<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/10/10 
// +----------------------------------------------------------------------


namespace app\admin\controller;


use think\Request;
use \app\admin\model\GoodsClassify as GoodsClassifyModel;

class GoodsClassify extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new GoodsClassifyModel();
    }

    public function index()
    {
        $classifyListOption = $this->currentModel->getClassifyListOption();
        $this->assign('classifyListOption', $classifyListOption);
        return $this->fetch();
    }

    /**
     * 菜单列表页面 获取数据
     * @throws \think\exception\DbException
     * @return array
     */
    public function getDataList()
    {
        $param = $this->request->param();
        $map = [];
        if (!empty($param['pid'])) {
            $map['pid'] = $param['pid'];
        }
        if (!empty($param['classify_name'])) {
            $map['classify_name'] = ['like', '%'.$param['classify_name'].'%'];
        }
        $count = $this->currentModel->where($map)->count();
        $list = $this->currentModel->where($map)
            ->field(true)
            ->field('pid as pid_name, id as goods_num')
            ->select();
        $list = \Tree::get_Table_tree($list, 'classify_name', 'id');
        foreach ($list as $key=>$val) {
            unset($list[$key]['child']);
        }
        $data = array_slice($list, ($param['page'] - 1) * $param['limit'], $param['limit']);
        return ['code'=>0, 'msg'=>'', 'count'=>$count, 'data'=>$data];
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
        $pid = $data['pid'] ?? 0;
        $list = $this->currentModel->field(true)->select();
        $classifyListOption = \Tree::get_option_tree($list, $pid, 'classify_name', 'id');
        $this->assign('classifyListOption', $classifyListOption);

        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }
        //验证数据
        $result = $this->validate($param, 'GoodsClassify');
        if ($result !== true) {
            $this->error($result);
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

    /**
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $goods = (new \app\admin\model\Product())->where('goods_classify_id', $id)->select();
        if ($goods) {
            $this->error('此分类下有商品，不能删除');
        }
        $this->currentModel->where('id', $id)->delete();
        $this->success('删除成功');
    }
}