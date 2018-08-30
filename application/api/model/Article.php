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
use app\common\model\RelationArticleCollect;
use app\common\model\RelationUserCollect;
use app\common\validate\BaseValidate;
use think\Cache;
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
    protected $commentRow = 10;

    protected $page = 1;

    protected $row = 10;

    protected $order = [
        0 => 'a.create_time desc',      //最新
        1 => 'pageview desc',           //人气
        2 => '',                        //最近
        3 => 'comment_total desc',      //回复
    ];

    /**
     * 附近的用户
     * @var array
     */
    protected $ids = [];

    /**
     * 设置页码和每页条目数
     * @param $page
     * @param $row
     */
    public function setPage($page, $row)
    {

        $this->page = $page;
        $this->row  = $row;
    }

    /**
     * 获取附近的用户
     * @return array
     */
    protected function getNearbyUsers()
    {

        //TODO 获取附近用户 [ids] 按距离排序，近的在前远的在后    (方法待完善)
        $this->ids = [26];
    }

    /**
     * 圈子  同城的 和已关注的用户的动态
     * @param string $classifyId
     * @param int $order
     * @return array
     */
    public function localArticleList($classifyId = '', $param)
    {

        $order = $param['order'];
        $this->getNearbyUsers();
        //已关注用户
        $users              = RelationUserCollect::where('user_id', user_info('id'))->column('other_user_id');
        $ids                = array_merge($this->ids, $users);
        $where['a.user_id'] = ['in', $ids];
        if ($classifyId) {
            $where['d.id'] = $classifyId;
        }
        if (array_key_exists('keyword', $param)) {
            $where['a.title'] = ['like', "%" . $param['keyword'] . "%"];
        }

        return $this->executeSelect($where, $order);
    }

    /**
     * 根据用户id获取圈子文章
     * @param $id
     * @return array
     */
    public function getListByUserId($id, $param)
    {

        $where = is_array($id) ? ['b.id' => ['in', $id]] : ['b.id' => $id];
        if (array_key_exists('keyword', $param)) {
            $where['a.title'] = ['like', "%" . $param['keyword'] . "%"];
        }

        return $this->executeSelect($where);
    }

    /**
     * 我收藏的文章
     * @return array
     */
    public function myCollectArticle()
    {

        $ids   = RelationArticleCollect::where('user_id', user_info('id'))->page($this->page, $this->row)->column('article_id');
        $where = ['a.id' => ['in', $ids]];

        return $this->executeSelect($where);
    }

    /**
     * 我关注的用户
     * @return mixed
     */
    public function myCollect()
    {

        return (new RelationUserCollect())->myCollect($this->page, $this->row);
    }

    /**
     * 关注我的
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function collectMe()
    {

        return (new RelationUserCollect())->collectMe($this->page, $this->row);
    }

    /**
     * 根据分类获取文章列表
     * @param $classifyId
     * @param $param
     * @return array
     * @throws \think\exception\DbException
     */
    public function getListByClassify($classifyId, $param)
    {

        if (!array_key_exists('order', $param)) {
            $param['order'] = 0;
        }
        if ($param['order'] == 2) {
            $this->getNearbyUsers();
        }
        $where = ['a.classify_id' => $classifyId];
        if (array_key_exists('keyword', $param)) {
            $where['a.title'] = ['like', "%" . $param['keyword'] . "%"];
        }

        return $this->executeSelect($where, $param['order']);
    }

    /**
     * 组装模型
     * @return object
     */
    private function recombination()
    {

        $map   = [
            'a.state'       => 1,
            'a.delete_time' => null,
            'b.state'       => 1,
            'b.delete_time' => null,
            'c.delete_time' => null,
            'd.state'       => 1,
            'd.delete_time' => null,
            //'e.delete_time'     => null
        ];
        $model = $this->alias('a')
            ->join('user b', 'a.user_id = b.id')
            ->join('article_classify d', 'a.classify_id = d.id')//分类
            ->join('article_comment c', 'a.id = c.article_id', 'LEFT')//评论
            ->join('relation_article_great e', 'a.id = e.article_id', 'LEFT')//点赞
            ->where($map);

        return $model;
    }

    /**
     * 执行查询
     * @param $where
     * @param int $order
     * @return array
     * @throws \think\exception\DbException
     */
    private function executeSelect($where, $order = 0)
    {

        $orderWord = $order == 2 ? 0 : $order;
        $orderBy   = $this->order[$orderWord];
        $data      = $this->recombination()
            ->where($where)
            ->field("a.id, a.title, b.id as user_id, b.user_name, b.avatar, a.create_time, d.classify_name, a.pageview, count(e.id) as great_total, count(c.id) as comment_total")
            ->group('a.id, c.article_id, e.article_id')
            //            ->page($this->page, $this->row)
            ->order($orderBy)
            ->select();
        if ($order == 2) {
            //按用户距离最近排序
            $sort = [];
            foreach ($data as $k => $v) {
                $data[$k]['sort'] = array_search($v['user_id'], $this->ids);
                $sort[$k]         = $data[$k]['sort'];
            }
            array_multisort($sort, SORT_ASC, $data);
        }
        $list = [];
        foreach ($data as $v) {
            if (array_key_exists('sort', $v)) {
                unset($v['sort']);
            }
            $v['img'] = $this->getContentImages($v['id']);
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
    private function getContentImages($articleId)
    {

        $contents = ArticleContent::all(['article_id' => $articleId]);
        $imgs     = [];
        foreach ($contents as $k => $v) {
            if (count($imgs) < $this->imgNum) {
                array_push($imgs, get_thumb_img($v->img));
            }
            if (count($imgs) == $this->imgNum) {
                break;
            }
        }

        return $imgs;
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

        $data            = $this->recombination()
            ->where('a.id', $id)
            ->field("a.id, a.user_id as user_id, b.user_name, b.avatar, a.create_time, a.music, d.classify_name, a.pageview, count(e.id) as great_total, count(c.id) as comment_total")
            ->group('e.id, c.id')
            ->find();
        $data['is_self'] = user_info('id') == $data['user_id'];
        //文章内容
        $data['content'] = ArticleContent::all(function ($query) use ($id) {

            $query->where('article_id', $id)->field('img, text, sort')->order('sort');
        });
        //文章评论
        $data['comments'] = (new ArticleComment())->getComments($id, 1, $this->commentRow);

        return $data;
    }

    /**
     * 根据id 获取部分详情 编辑时使用
     * @param $id
     * @throws \think\exception\DbException
     * @return array
     */
    public function getArticleContent($data)
    {

        $articleId   = $data['articleId'];
        $result      = [
            'id'         => '',
            'music'      => '',
            'music_name' => '',
            'items'      => [],
        ];
        $cacheData   = $result;
        $contentData = Db::query("SELECT id,user_id,music,music_name FROM `article` WHERE id = {$articleId}");
        if (!empty($contentData)) {
            $contentItemData = Db::query("SELECT id,text,img FROM `article_content` WHERE article_id = {$articleId} ORDER BY sort DESC,id ASC");
            $result          = $contentData[0];
            $result['items'] = [];
            if (!empty($contentItemData)) {
                $result['items'] = $contentItemData;
            }
            $cacheData = [
                'id'         => $contentData[0]['id'],
                'music'      => $contentData[0]['music'],
                'music_name' => $contentData[0]['music_name'],
                'items'      => $result['items'],
            ];
        }
        // 重置缓存
        Cache::set('article_cache_' . $articleId, json_encode($cacheData));

        return $result;
    }

    public function queryArticle($data)
    {

        $order      = $data['order'];
        $classifyId = $data['classifyId'];
        $keyword    = $data['keyword'];
        $userId     = $data['userId'];
        $selfUserId = $data['selfUserId'];
        $isSelf     = $data['isSelf'];
        $this->getNearbyUsers();
        //已关注用户
        $users              = RelationUserCollect::where('user_id', user_info('id'))->column('other_user_id');
        $ids                = array_merge($this->ids, $users);
        $where['a.user_id'] = ['in', $ids];
        if (!empty($classifyId)) {
            $where['d.id'] = $classifyId;
        }
        if (!empty($keyword)) {
            $where['a.title'] = ['like', "%" . $keyword . "%"];
        }
        if (!empty($userId)) {
            $where['b.id'] = is_array($userId) ? ['in', $userId] : $userId;
        }
        if (!empty($isSelf)) {
            $where['b.id'] = $selfUserId;
        }

        return $this->executeSelect($where, $order);
    }

}