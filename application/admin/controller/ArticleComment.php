<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/8 
// +----------------------------------------------------------------------


namespace app\admin\controller;

use think\Db;
use think\Request;
use app\common\model\ArticleComment as ArticleCommentModel;

/**
 * 文章评论
 * Class ArticleComment
 * @package app\admin\controller
 */
class ArticleComment extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new ArticleCommentModel();//实例化当前模型
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
        $map = $this->getDataListMap();
        return $this->currentModel
            ->where($map)
            ->order('id desc')
            ->layTable(['user_name', 'content_decode']);
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        $map = [];
        if (!empty($param['user_name'])) {
            $users = Db::table('user')->where('user_name', 'like', "%{$param['user_name']}%")->column('id');
            $map['user_id'] = ['in', implode(',', $users)];
        }
        if (isset($param['content']) && $param['content'] !== '') {
            $map['content'] = ['like', "%{$param['content']}%"];
        }
        if (isset($param['article_id']) && $param['article_id'] !== '') {
            $map['article_id'] = trim($param['article_id']);
        }
        return $map;
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

            $ArticleContent = new ArticleContent();
            $content = $ArticleContent->where('article_id', $param['id'])->order('sort')->select();
            $this->assign('content', $content);

            $data = $data->toArray();
            $this->assign('data', $data);
        }
        //分类名称列表
        $classifyList = Db::name('article_classify')->select();
        $this->assign('classifyList', $classifyList);

        //作者列表
        $userList = Db::name('user')->select();
        $this->assign('userList', $userList);
        return $this->fetch();
    }

    /**
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $info = $this->currentModel->get($id);
        $info->backups = $info->content;
        $info->content = '该评论已删除';
        $info->save();
        $this->success('操作成功');
    }

}