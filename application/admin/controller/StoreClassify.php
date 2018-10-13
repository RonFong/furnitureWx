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
use app\common\model\StoreClassify as StoreClassifyModel;

class StoreClassify extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new StoreClassifyModel();
    }

    public function index()
    {
        return $this->fetch();
    }

    public function getDataList()
    {
        $param = $this->request->param();
        $map = [];
        if (!empty($param['name'])) {
            $map['name'] = ['like', "%{$param['name']}%"];
        }
        if (!empty($param['parent_id'])) {
            $map['parent_id'] = $param['parent_id'];
        }
        if (!empty($param['parent_name'])) {
            $parentName = trim($param['parent_name']);
            $ids = $this->currentModel->where('name', 'like', "%$parentName%")->column('id');
            $map['parent_id'] = ['in', $ids];
        }
        return $this->currentModel->where($map)->order('parent_id, sort')->layTable(['parent_name', 'state_text']);
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
            $this->assign('data', $data);
        }

        //分类名称列表
        $value_id = !empty($data['parent_id']) ? $data['parent_id'] : 0;
        $classifyList = $this->currentModel->select();
        $classifyList = \Tree::get_option_tree($classifyList, $value_id, 'name', 'id', 'parent_id');
        $this->assign('classifyList', $classifyList);
        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'ArticleClassify');
        if ($result !== true) {
            $this->error($result);
        }

        try {
            //图片不为空，开始处理图片
            if (!empty($param['classify_img'])) {
                $old = !empty($param['id']) ? Db::name('article_classify')->where('id', $param['id'])->value('classify_img') : '';
                if (empty($old) || $param['classify_img'] != $old) {
                    $param['classify_img'] = current(imgTempFileMove([$param['classify_img']], 'img/article/'));//从临时文件夹移动图片
                    if (!empty($param['id'])) {
                        $count = Db::name('article_classify')->where('classify_img', $old)->count();//编辑且换图，则删除旧图片
                        //若其他地方没使用该旧图片，则删除
                        if ($count == 1){
                            delete_file($old);
                        }
                    }
                }
            }

            //保存数据
            $this->currentModel->save($param);
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        $this->success('保存成功！', 'edit?id='.$this->currentModel->id);
    }
}