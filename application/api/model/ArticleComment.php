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
use think\Cache;
use think\Db;

/**
 * 文章评论 （圈子）
 * Class ArticleComment
 * @package app\api\model
 */
class ArticleComment extends CoreArticleComment
{

    public function saveData($data)
    {

        $data['user_id'] = user_info('id');
        if ($this->save($data)) {
            $result = [
                'user_name'   => user_info('user_name'),
                'content'     => $data['content'],
                'create_time' => date('m-d H:i', time()),
            ];
            //所回复的评论的发布者
            if (array_key_exists('parent_id', $data)) {
                $info                       = self::with('appendUserName')->where('id', $this->data['parent_id'])->find();
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
            ->order('a.create_time')
            ->page($page, $row)
            ->select();
        $list     = [];
        foreach ($comments as $v) {
            $v = $this->recursionComment($v);
            array_push($list, $v);
        }

        return $list;
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
        $info = self::with('appendUserName')
            ->where(['parent_id' => $reply['id'], 'state' => 1])
            ->find();
        if ($info) {
            $content = [
                'id'                   => $info->id,
                'user_id'              => $info->user_id,       //回复人id
                'user_name'            => $info->user_name,     //回复人昵称
                'respondent_user_name' => '',                   //被回复人昵称  （如果当前为该评论的第一条回复，则被回复人为空）
                'reply_content'        => $info->content,
            ];
            if (!empty($v['child'])) {
                $content['respondent_user_name'] = $reply['user_name'] ?? $reply['user_name'];
            }
            array_push($v['child'], $content);
            $this->recursionComment($v, $info);
        }

        return $v;
    }

    public static function setCache($data)
    {

        $articleId  = $data['articleId'];
        $userId     = $data['userId'];
        $music      = $data['music'];
        $musicName  = $data['musicName'];
        $classifyId = $data['classifyId'];
        $itemKey    = $data['itemKey'];
        $text       = $data['text'];
        $img        = $data['img'];
        if (empty($articleId)) {
            $cacheTmpData = Cache::get('article_cache_tmp_' . $userId);
            if (empty($cacheTmpData)) {
                $cacheTmpData = [
                    'classify_id' => !empty($classifyId) ? $classifyId : '',
                    'music'       => $music !== false ? $music : '',
                    'music_name'  => $musicName !== false ? $musicName : '',
                    'items'       => [
                        [
                            'id'   => '',
                            'text' => '',
                            'img'  => '',
                        ],
                    ],
                ];
            } else {
                switch ($data['type']) {
                    case 1:// 编辑
                        $cacheTmpData['music']       = $music !== false ? $music : $cacheTmpData['music'];
                        $cacheTmpData['music_name']  = $musicName !== false ? $musicName : $cacheTmpData['music_name'];
                        $cacheTmpData['classify_id'] = !empty($classifyId) ? $classifyId : $cacheTmpData['classify_id'];
                        break;
                    case 2:// addBox 添加
                        $pushData = [
                            'id'   => '',
                            'text' => '',
                            'img'  => '',
                        ];
                        array_push($cacheTmpData['items'], $pushData);
                        break;
                    case 3:// delBox 删除
                        array_splice($cacheTmpData['items'], $data['itemKey'], 1);
                        break;
                }
            }
            Cache::set('article_cache_tmp_' . $userId, json_encode($cacheTmpData));
        } else {
            $cacheData = Cache::get('article_cache_' . $articleId);
            if (empty($cacheData)) {
                $articleData        = Db::query("SELECT * FROM `article` WHERE id = {$articleId}");
                $articleContentData = Db::query("SELECT * FROM `article_content` WHERE article_id = {$articleId}");
                $cacheData = [
                    'classify_id' => !empty($articleData) ? $articleData['classify_id'] : '',
                    'music'       => !empty($articleData) ? $articleData['music'] : '',
                    'music_name'  => !empty($articleData) ? $articleData['music_name'] : '',
                    'items'       => !empty($articleContentData) ? $articleContentData : [],
                ];
                $cacheData['music']       = $music !== false ? $music : $cacheData['music'];
                $cacheData['music_name']  = $musicName !== false ? $musicName : $cacheData['music_name'];
                $cacheData['classify_id'] = !empty($classifyId) ? $classifyId : $cacheData['classify_id'];
            } else {
                switch ($data['type']) {
                    case 1:// 编辑
                        $cacheData['music']       = $music !== false ? $music : $cacheData['music'];
                        $cacheData['music_name']  = $musicName !== false ? $musicName : $cacheData['music_name'];
                        if (!empty($cacheData['items'])) {
                            foreach ($cacheData['items'] AS $key => &$value) {
                                if ($key == $data['itemKey']) {
                                    if ($data['text'] !== false) {
                                        $value['text'] = $data['text'];
                                    }
                                    if ($data['img'] !== false) {
                                        $value['img'] = $data['img'];
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                    case 2:// addBox 添加
                        $pushData = [
                            'id'   => '',
                            'text' => '',
                            'img'  => '',
                        ];
                        array_push($cacheData['items'], $pushData);
                        break;
                    case 3:// delBox 删除
                        array_splice($cacheData['items'], $data['itemKey'], 1);
                        break;
                }
            }
            Cache::set('article_cache_' . $articleId, json_encode($cacheData));
        }

        return true;
    }

    public static function getCache($data)
    {

        $articleId = $data['articleId'];
        $itemKey   = $data['itemKey'];
        $userId    = $data['userId'];
        $result    = [
            'classify_id' => '',
            'music'       => '',
            'music_name'  => '',
            'items'       => [
                [
                    'id'   => '',
                    'text' => '',
                    'img'  => '',
                ],
            ],
        ];
        if (empty($articleId)) {
            $cacheTmpData = Cache::get('article_cache_tmp_' . $userId);
            if (empty($cacheTmpData)) {
                Cache::set('article_cache_tmp_' . $userId, json_encode($result));
                switch ($data['type']) {
                    // 获取所有缓存
                    case 1:
                        break;
                    // 获取单个item缓存
                    case 2:
                        $result = $result['items'][0];
                        break;
                }
            } else {
                switch ($data['type']) {
                    // 获取所有缓存
                    case 1:
                        $result = $cacheTmpData;
                        break;
                    // 获取单个item缓存
                    case 2:
                        $result = $cacheTmpData['items'][$itemKey];
                        break;
                }
            }

        } else {
            $cacheData = Cache::get('article_cache_' . $articleId);
            if ($cacheData) {
                switch ($data['type']) {
                    // 获取所有缓存
                    case 1:
                        $result = $cacheData;
                        break;
                    // 获取单个item缓存
                    case 2:
                        $result = $cacheData['items'][$itemKey];
                        break;
                }
            }
        }

        return $result;
    }
}