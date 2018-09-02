<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\ArticleClassify as CoreArticleClassify;
use think\Db;
use think\Request;

class ArticleClassify extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreArticleClassify();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
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
        $param = $this->request->param();
        if (!empty($param['classify_name'])) {
            $map['classify_name'] = ['like', '%' . $param['classify_name'] . '%'];//分类名称
        }
        if (empty($map)) {
            $map[] = ['exp', '1=1'];
        }

        $count = $this->currentModel->where($map)->count();
        $list = $this->currentModel->where($map)->order('sort asc,id desc')->select();
        $list = collection($list)->append(['parent_id', 'parent_text', 'state_text'])->toArray();
        $list = \Tree::get_Table_tree($list, 'classify_name', 'id', 'parent_id');

        foreach ($list as $key=>$val) {
            unset($list[$key]['child']);
        }
        $list = array_slice($list, ($param['page'] - 1) * $param['limit'], $param['limit']);
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
            $data = $data->append(['parent_id', 'state'])->toArray();
            $this->assign('data', $data);
        }

        //分类名称列表
        $value_id = !empty($data['parent_id']) ? $data['parent_id'] : 0;
        $classifyList = Db::name('article_classify')->select();
        $classifyList = \Tree::get_option_tree($classifyList, $value_id, 'classify_name', 'id', 'parent_id');
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

