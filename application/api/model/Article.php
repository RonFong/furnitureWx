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
     * 圈子  同城的 和已关注的用户的动态
     * @param int $page
     * @param int $row
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function localArticleList($page = 1, $row = 10)
    {
        //TODO 获取附近用户 ids 控制用户数   (方法待完善)
        $ids = [15,16,17];

        //已关注用户
        $users = RelationUserCollect::where('user_id', user_info('id'))->column('other_user_id');
        $ids = array_merge($ids, $users);

        $map = [
            'a.state'           => 1,
            'a.delete_time'     => null,
            'b.id'              => ['in', $ids],
            'b.state'           => 1,
            'b.delete_time'     => null,
            'c.delete_time'     => null,
            'd.state'           => 1,
            'd.delete_time'     => null,
        ];
        $data = Db::table('article')
            ->alias('a')
            ->join('user b', 'a.user_id = b.id')
            ->join('article_comment c', 'a.id = c.article_id', 'LEFT')          //评论
            ->join('article_classify d', 'a.classify_id = d.id')                //分类
            ->join('relation_article_great e', 'a.id = e.article_id', 'LEFT')   //点赞
            ->where($map)
            ->field("a.id, b.user_name, b.avatar, a.create_time, d.classify_name, a.pageview, count(e.id) as great_total, count(c.id) as comment_total")
            ->group('a.id')
            ->page($page, $row)
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
                $result['text'] = mb_strlen($v->text) > $this->textLength ? mb_substr($v->text, 0, $this->textLength, 'utf-8') : $v->text;
            }
            if (count($result['img']) == $this->imgNum) {
                break;
            } else {
                array_push($result['img'], $v->img);
            }
        }
        return $result;
    }
}