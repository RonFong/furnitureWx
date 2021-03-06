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

use app\common\model\ArticleComment as CoreArticleComment;
use think\Db;

/**
 * 文章评论 （圈子）
 * Class ArticleComment
 * @package app\api\model
 */
class ArticleComment extends CoreArticleComment
{

    /**
     * Emoji 表情符
     * @param $value
     * @return string
     */
    public function getContentAttr($value)
    {
        return $this->emojiDecode($value);
    }

    public function setContentAttr($value)
    {
        return $this->emojiEncode($value);
    }

    public function saveData($data)
    {

        $data['user_id'] = user_info('id');
        if ($this->save($data)) {
            $result = [
                'user_name'   => user_info('user_name'),
                'content'     => $this->emojiDecode($data['content']),
                'create_time' => '刚刚'
            ];
            //所回复的评论的发布者
            if (array_key_exists('parent_id', $data)) {
                $info = $this->where('id', $this->data['parent_id'])
                    ->field(true)
                    ->field('user_id as user_name')
                    ->find();
                $result['parent_user_name'] = $info->user_name;
            }

            return $result;
        }

        return false;
    }

    /**
     * 根据文章id获取评论
     * @param $articleId
     * @param $page
     * @param $row
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getComments($articleId, $page, $row)
    {

        $map      = [
            'a.article_id'  => $articleId,
            'a.state'       => 1,
            'a.parent_id'   => 0,
            'a.delete_time' => null,
            'b.state'       => 1,
            'b.delete_time' => null,
//            'c.delete_time' => null
        ];
        $comments = Db::table('article_comment')
            ->alias('a')
            ->join('user b', 'b.id = a.user_id')
            ->join('relation_comment_great c', 'c.comment_id = a.id', 'LEFT')
            ->where($map)
            ->field('a.id, b.id as user_id, b.user_name, b.avatar, a.content, count(c.id) as great_total, a.create_time')
            ->group('a.id')
            ->order('great_total desc, a.create_time')
            ->page($page, $row)
            ->select();

        $list     = [];
        foreach ($comments as $v) {
            $v['user_name'] = $this->emojiDecode($v['user_name']);
            $comment = $this->moreCommentReply($v['id']);
            if (!$comments) {
                continue;
            }
            $v = $comment['comment'];
            $v['content'] = $this->emojiDecode($v['content']);
            $v['child'] = array_slice($comment['reply'], 0, 3);
            $v['child_num'] = count($comment['reply']);
            array_push($list, $v);
        }
        return $list;
    }


    /**
     * 获取评论的所有回复
     * @param $commentId
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function moreCommentReply($commentId)
    {
        $child = Db::table('article_comment')
            ->alias('a')
            ->join('user b', 'a.user_id = b.id')
            ->where(['a.parent_id' => $commentId, 'a.state' => 1])
            ->where('a.delete_time is null')
            ->field('a.id, a.content, b.user_name')
            ->select();
        $data = [];
        if ($child) {
            $tempData = [];
            foreach ($child as $v) {
                $v['user_name'] = $this->emojiDecode($v['user_name']);
                $v['content'] = $this->emojiDecode($v['content']);
                $v['respondent_user_name'] = '';  // 不是对回复的回复，回复人昵称值为空
                $tempData[] = $this->recursionComment($v);
            }
            unset($v);
            foreach ($tempData as $k => $v) {
                $child = $v['child'];
                unset($v['child']);
                $v['is_great'] = $this->isGreat($v['id']);
                array_push($data, $v);
                if ($child) {
                    foreach ($child as $vv) {
                        $vv['content'] = $this->emojiDecode($vv['content']);
                        array_push($data, $vv);
                    }
                }
            }
        }
        $result['comment'] = Db::table('article_comment')
            ->alias('a')
            ->join('user b', 'b.id = a.user_id')
            ->join('relation_comment_great c', 'c.comment_id = a.id', 'LEFT')
            ->where("a.id = $commentId")
            ->field('a.id, b.id as user_id, b.user_name, b.avatar, a.content, count(c.id) as great_total, a.create_time')
            ->group('a.id')
            ->find();
        $result['comment']['user_name'] = $this->emojiDecode($result['comment']['user_name']);
        $result['comment']['content'] = $this->emojiDecode($result['comment']['content']);
        $result['comment']['is_great'] = $this->isGreat($commentId);
        $result['comment']['create_time'] = time_format_for_humans($result['comment']['create_time']);
        $result['reply'] = $data;
        return $result;
    }

    /**
     * 递归获取评论的回复
     * @param $v
     * @param array $reply
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function recursionComment(&$v, $reply = [])
    {

        if (empty($reply)) {
            $v['child']  = [];
            $reply['id'] = $v['id'];
        }
        $info = (new self())
            ->where(['parent_id' => $reply['id'], 'state' => 1])
            ->field(true)
            ->field('user_id as user_name')
            ->find();

        if ($info) {
            $content = [
                'id'                   => $info['id'],
                'user_id'              => $info['user_id'],       //回复人id
                'user_name'            => $this->emojiDecode($info['user_name']),     //回复人昵称
                'respondent_user_name' => $reply['respondent_user_name'] ?? $v['user_name'],                   //被回复人昵称  （如果当前为该评论的第一条回复，则被回复人为空）
                'content'              => $this->emojiDecode($info['content']),
                'is_great'             => $this->isGreat($info['id'])
            ];
            if (!empty($v['child'])) {
                $content['respondent_user_name'] = $reply['user_name'] ? $reply['user_name'] : $v['user_name'];
            }
            array_push($v['child'], $content);

            $this->recursionComment($v, $info);
        }
        return $v;
    }

    /**
     * 是否点赞评论
     * @param $id
     * @return int
     */
    private function isGreat($id)
    {
         $isGreat = Db::table('relation_comment_great')->where(['user_id' => user_info('id'), 'comment_id' => $id])->find();
        return $isGreat ? 1 : 0;
    }




}