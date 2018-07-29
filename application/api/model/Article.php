<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\api\model;

use app\common\model\Article as CoreArticle;
use app\common\model\RelationUserCollect;
use think\Db;

class Article extends CoreArticle
{
    /**
     * 在列表中，文字显示的字数长度
     * @var int
     */
    private $textLength = 150;

    /**
     * 在列表中，显示的图片张数
     * @var int
     */
    private $imgNum = 3;

    /**
     * 默认显示评论数
     * @var int
     */
    private $commentRow = 10;

    private $page = 1;

    private $row = 10;

    /**
     * 圈子  同城的 和已关注的用户的动态
     * @param int $page
     * @param int $row
     * @return array
     * @throws \think\exception\DbException
     */
    public function localArticleList($page = 1, $row = 10)
    {
        $this->page = $page;
        $this->row = $row;

        //TODO 获取附近用户 ids 控制用户数   (方法待完善)
        $ids = [15,16,17];

        //已关注用户
        $users = RelationUserCollect::where('user_id', user_info('id'))->column('other_user_id');
        $ids = array_merge($ids, $users);
        return $this->executeSelect($ids);
    }


    /**
     * 根据用户id获取圈子文章
     * @param $id
     * @param $page
     * @param $row
     * @return array
     * @throws \think\exception\DbException
     */
    public function getListByUserId($id, $page, $row)
    {
        $where = is_array($id) ? ['b.id' => ['in', $id]] : ['b.id' => $id];
        $this->page = $page;
        $this->row = $row;

        return $this->executeSelect($where);
    }

    /**
     * 根据分类获取文章列表
     * @param $classifyId
     * @param $page
     * @param $row
     * @return array
     * @throws \think\exception\DbException
     */
    public function getListByClassify($classifyId, $page, $row)
    {
        $this->page = $page;
        $this->row = $row;
        $where = ['a.classify_id' => $classifyId];
        return $this->executeSelect($where);
    }

    /**
     * 组装模型
     * @return object
     */
    private function recombination()
    {
        $map = [
            'a.state'           => 1,
            'a.delete_time'     => null,
            'b.state'           => 1,
            'b.delete_time'     => null,
            'c.delete_time'     => null,
            'd.state'           => 1,
            'd.delete_time'     => null,
            'e.delete_time'     => null
        ];
        $model = $this->alias('a')
            ->join('user b', 'a.user_id = b.id')
            ->join('article_classify d', 'a.classify_id = d.id')                //分类
            ->join('article_comment c', 'a.id = c.article_id', 'LEFT')          //评论
            ->join('relation_article_great e', 'a.id = e.article_id', 'LEFT')   //点赞
            ->where($map);
        return $model;
    }

    /**
     * 执行查询
     * @param $where
     * @return array
     * @throws \think\exception\DbException
     */
    private function executeSelect($where)
    {
        $data = $this->recombination()
            ->where($where)
            ->field("a.id, b.user_name, b.avatar, a.create_time, d.classify_name, a.pageview, count(e.id) as great_total, count(c.id) as comment_total")
            ->group('a.id, c.article_id, e.article_id')
            ->page($this->page, $this->row)
            ->order('a.create_time')
            ->select();
        $list = [];
        foreach ($data as $v) {
            $v['content'] = $this->getFirstTextAndImages($v['id']);
            array_push($list, $v);
        }
        return $list;
    }

    /**
     * 获取 文章的第一个内容块的文字和 前三张图片
     * @param $articleId
     * @return array
     * @throws \think\exception\DbException
     */
    private function getFirstTextAndImages($articleId)
    {
        $contents = ArticleContent::all(['article_id' => $articleId]);
        $result = [
            'text' => '',
            'img'  => []
        ];
        foreach ($contents as $k => $v) {
            if (empty($result['text']) && !empty($v->text)) {
                //文字内容过长则只截取部分
                $result['text'] = mb_strlen($v->text) > $this->textLength ? mb_substr($v->text, 0, $this->textLength, 'utf-8') : $v->text;
            }
            if (count($result['img']) < $this->imgNum) {
                array_push($result['img'], get_thumb_img($v->img));
            }
            if (!empty($result['text']) && count($result['img']) == $this->imgNum) {
                break;
            }
        }
        return $result;
    }

    /**
     * 分享数 + 1
     * @param $id
     * @return int|true
     * @throws \think\Exception
     */
    public function share($id)
    {
        return $this->where('id', $id)->setInc('share');
    }

    /**
     * 根据id 获取详情
     * @param $id
     * @throws \think\exception\DbException
     * @return array
     */
    public function details($id)
    {
        $data = $this->recombination()
            ->where('a.id', $id)
            ->field("a.id, a.user_id as user_id, b.user_name, b.avatar, a.create_time, a.music, d.classify_name, a.pageview, count(e.id) as great_total, count(c.id) as comment_total")
            ->group('e.id, c.id')
            ->find();
        $data['is_self'] = user_info('id') == $data['user_id'];
        //文章内容
        $data['content'] = ArticleContent::all(function ($query) use ($id){
            $query->where('article_id', $id)->field('img, text, sort')->order('sort');
        });
        //文章评论
        $data['comments'] = (new ArticleComment())->getComments($id, 1, $this->commentRow);
        return $data;
    }

}