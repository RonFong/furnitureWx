<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/5/19 
// +----------------------------------------------------------------------


namespace app\admin\controller;

use think\Db;
use think\Request;

/**
 * 经营类别
 * Class ShopClassify
 * @package app\admin\controller
 */
class ShopClassify extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new \app\common\model\ShopClassify();
    }

    public function index()
    {
        return $this->fetch();
    }

    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        return $this->currentModel->layTable();
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $data = [];
        if ($this->request->param('id')) {
            $data = $this->currentModel->where('id', $this->request->param('id'))->find();
        }
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 保存
     */
    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'ShopClassify');
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

    public function delete($id)
    {
        $shops = Db::table('shop')->where('delete_time is null')->where('classify_id', $id)->select();
        if ($shops) {
            $this->error('该类别下有商家，不能删除');
        }
        $this->success('删除成功！');
    }
}