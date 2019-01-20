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

use app\api\service\ArticleReadHistory;
use app\common\model\Article as CoreArticle;
use app\common\model\RelationUserCollect;
use app\common\model\UserLocation;
use app\common\validate\BaseValidate;
use think\Db;

class Article extends CoreArticle
{

    /**
     * 显示范围 (公里)
     * @var int
     */
    private $distance = 10000;

    /**
     * 在列表中，显示的图片/视频张数
     * @var int
     */
    private $imgNum = 9;

    /**
     * 默认显示评论数
     * @var int
     */
    protected $commentRow = 5;

    /**
     * 用户经纬度信息
     * @var array
     */
    protected $location = [];

    /**
     * 在列表中显示的字数
     * @var int
     */
    protected $showWordNum = 60;


    public function getTitleAttr($value)
    {
        return $this->emojiDecode($value);
    }

    public function setTitleAttr($value)
    {
        return $this->emojiEncode($value);
    }

    /**
     * 获取用户当前经纬度
     * @param $query
     * @return mixed
     */
    protected function getLocation($query)
    {
        if (!$this->location) {
            $location = (new UserLocation())->where(['user_id' => user_info('id')])->order('id desc')->find();
            if (!$location) {
                (new BaseValidate())->error(['code' => 0, 'msg' => '位置信息错误', 'errorCode' => 999]);
            }
            $this->location = $location;
        }
        return $this->location[$query];
    }

    /**
     * 创建圈子文章
     * @param $param
     * @return array
     * @throws \app\lib\exception\BaseException
     */
    public function createData($param)
    {
        try {
            Db::startTrans();
            $param['user_id'] = user_info('id');
            $param['lng'] = $this->getLocation('lng');
            $param['lat'] = $this->getLocation('lat');
            $this->save($param);
            foreach ($param['content'] as $k => $v) {
                if (!empty($v['text']) || !empty($v['img']) || !empty($v['video'])) {
                    $v['article_id'] = $this->id;
                    $v['sort'] = $k;
                    if (array_key_exists('style', $v) && !empty($v['style'])) {
                        $v['style'] = json_encode($v['style']);
                    }
                    $itemResult = (new ArticleContent())->save($v);
                    if (empty($v['id']) && !$itemResult) {
                        exception('内容块数据写入失败：'.json_encode($v));
                    }
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
        return ['id' => $this->id];
    }

    /**
     * 修改
     * @param $param
     * @return bool
     * @throws \app\lib\exception\BaseException
     */
    public function updateData($param)
    {
        try {
            Db::startTrans();
            if (isset($param['is_draft']) && $param['is_draft'] == 0) {
                //将文章草稿发布，经纬度 改为当前位置
                $article = self::get($param['id']);
                if ($article->is_draft == 2) {
                    $param['lat'] = user_info('lat');
                    $param['lng'] = user_info('lng');
                }
            }
            $this->save($param);
            $itemModel = new ArticleContent();
            $itemIds = $itemModel->where('article_id', $param['id'])->column('id');
            //id 存在，但内容为空的，删除
            $updateIds = [];
            foreach ($param['content'] as $k => $v) {
                if (!empty($v['text']) || !empty($v['img']) || !empty($v['video'])) {
                    if (empty($v['id'])) {
                        unset($v['id']);
                    } else {
                        array_push($updateIds, $v['id']);
                    }
                    $v['content_id'] = $param['id'];
                    $v['sort'] = $k;
                    if (array_key_exists('style', $v) && !empty($v['style'])) {
                        $v['style'] = json_encode($v['style']);
                    }
                    $v['article_id'] = $param['id'];
                    (new ArticleContent())->save($v);
                }
            }
            //删除
            $ids = array_diff($itemIds, $updateIds);
            if ($ids) {
                $itemModel->where('id', 'in', implode(',', $ids))->delete();
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
        return true;
    }

    /**
     * 文章列表
     * @param $param
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function list($param)
    {
        //TODO 是否显示已拉黑用户的文章动态？ 暂不处理!!!

        $pageData = format_page($param['page'] ?? 0, $param['row'] ?? 10);


        $field = "s.id, s.user_id, s.title, s.classify_id, s.read_num , s.share_num, s.create_time, s.distance ";
        $where = " `state` = 1 and `delete_time` is null ";
        if (!empty($param['classify_id'])) {
            $where .= "and classify_id = {$param['classify_id']} ";
        }
        if (!empty($param['keyword'])) {
            $keyword = trim($param['keyword']);
            $where .= " and title like '%$keyword%'";
        }
        if (!empty($param['user_id'])) {
            $where .= " and user_id = {$param['user_id']}";
        }

        if (!empty($param['is_draft'])) {
            $where .= " and is_draft = {$param['is_draft']}";
        } else {
            $where .= " and is_draft = 0";
        }

        if (!empty($param['ids'])) {
            $ids = implode(',', $param['ids']);
            $where .= " and id in ($ids) ";
        }

        $order = 's.create_time DESC';
        if (!empty($param['order_by'])) {
            if ($param['order_by'] == 'distance') {
                $order = "s.distance";
            } else {
                $order = "s.{$param['order_by']} DESC";
            }
        }
        $sql = "select {$field} from (
                select *,(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$this->getLocation('lng')}-lng)/360),2)+COS(PI()*33.07078170776367/180)* COS(lat * PI()/180)*POW(SIN(PI()*({$this->getLocation('lat')}-lat)/360),2)))) as distance 
                from `article` 
                where {$where}) as s 
                where s.distance <= {$this->distance}
                order by {$order} limit {$pageData['page']}, {$pageData['row']}";
        $list = Db::query($sql);

        foreach ($list as $k => $v) {
            $list[$k]['classify_name'] = Db::table('article_classify')->where('id', $v['classify_id'])->value('classify_name');
            unset($list[$k]['classify_id']);
            $list[$k]['distance'] = $v['distance'] >= 1 ? round($v['distance'], 1) . '公里' : ($v['distance'] * 1000 <= 100 ? '100米内' : round($v['distance'] * 1000) . '米');
            $user = Db::table('user')->where('id', $v['user_id'])->find();
            $list[$k]['user_name'] = $this->emojiDecode($user['user_name']);
            $list[$k]['avatar'] = $user['avatar'];
            //2019-01-16 去除标题，改为第一段内容文字
            $content = Db::table('article_content')->where(['article_id' => $v['id'], 'type' => 1])->value('text');
            $content = $content ? (mb_strlen($content) <= $this->showWordNum ? $content : mb_substr($content, 0, $this->showWordNum) . '...') : '';
            $list[$k]['title'] = $this->emojiDecode($content);
            $list[$k]['collect_num'] = Db::table('relation_article_collect')->where('article_id', $v['id'])->count();
            $list[$k]['great_num'] = Db::table('relation_article_great')->where('article_id', $v['id'])->count();
            $list[$k]['comment_num'] = Db::table('article_comment')
                ->where(['article_id' => $v['id'], 'parent_id' => 0, 'state' => 1])
                ->where('delete_time is null')
                ->count();
            $list[$k]['create_time'] = time_format_for_humans($v['create_time']);
            $list[$k]['content'] = $this->getArticleContentOnList($v['id']);
            $list[$k]['is_collect'] = $this->isCollect(user_info('id'), $v['id']);
            $list[$k]['is_great'] = $this->isGreat(user_info('id'), $v['id']);
        }
        return $list;
    }

    /**
     * 是否收藏文章
     * @param $userId
     * @param $articleId
     * @return int
     */
    private function isCollect($userId, $articleId)
    {
        $result = Db::table('relation_article_collect')
            ->where(['user_id' => $userId, 'article_id' => $articleId])
            ->find();
        return $result ? 1 : 0;
    }

    /**
     * 是否点赞文章
     * @param $userId
     * @param $articleId
     * @return int
     */
    private function isGreat($userId, $articleId)
    {
        $result = Db::table('relation_article_great')
            ->where(['user_id' => $userId, 'article_id' => $articleId])
            ->find();
        return $result ? 1 : 0;
    }

    /**
     * 获取文章在列表中索要显示的图片
     * @param $articleId
     * @return array
     * @throws \think\exception\DbException
     */
    private function getArticleContentOnList($articleId)
    {
        $content = ArticleContent::all(function ($query) use ($articleId) {
            $query->where('article_id', $articleId)
                ->field('type, img_thumb_small, video as video_snapshot')
                ->order('sort');
        });
        $imgList = [];
        $num = 0;
        foreach ($content as $v) {
            if ($num >= $this->imgNum) {
                break;
            }
            if ($v['type'] == 2 || $v['type'] == 3) {
                //文章列表中显示的图片
                $v['img_thumb_small'] = $v['type'] == 2 ? $v['img_thumb_small'] : $v['video_snapshot'];
                array_push($imgList, $v);
                $num ++;
            }
        }
        return $imgList;
    }


    /**
     * 文章详情
     * @param $id
     * @return array|null|static
     * @throws \Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function details($id)
    {
        $data = self::get(function ($query) use ($id) {
            $query->where('id', $id)
                ->field('id, user_id, is_draft, classify_id as classify_name, title, music, music_name, read_num, share_num, id as comment_num, create_time');
        });
        if (!$data) {
            exception('文章不存在');
        }
        $data = $data->toArray();
        $user = Db::table('user')->where('id', $data['user_id'])->find();
        $data['user_name'] = $this->emojiDecode($user['user_name']);
        $data['avatar'] = $user['avatar'];
        $data['create_time'] = time_format_for_humans(strtotime($data['create_time']));

        $data['content'] = ArticleContent::all(function ($query) use ($id) {
            $query->where('article_id', $id)
                ->field(true)
                ->field('video as video_snapshot, video as video_snapshot_auto')
                ->field('delete_time', true)
                ->order('sort');
        });
        $data['comment'] = (new ArticleComment())->getComments($id, 0, $this->commentRow);
        $data['is_collect'] = Db::table('relation_article_collect')->where(['user_id' => user_info('id'), 'article_id' => $id])->find() ? 1 : 0;
        $data['is_great'] = Db::table('relation_article_great')->where(['user_id' => user_info('id'), 'article_id' => $id])->find() ? 1 : 0;
        $data['collect_count'] = Db::table('relation_article_collect')->where('article_id', $id)->count();
        $data['great_count'] = Db::table('relation_article_great')->where('article_id', $id)->count();
        //用户关注
        if ($data['user_id'] == user_info('id')) {
            //不能关注自己
            $data['user_is_collect'] = -1;
        } else {
            $isCollect = Db::table('relation_user_collect')
                ->where(['user_id' => user_info('id'), 'other_user_id' => $data['user_id']])
                ->find();
            $data['user_is_collect'] = $isCollect ? 1 : 0;
        }

        //记录阅读历史 和 阅读数
        ArticleReadHistory::record($id);

       return $data;
    }


    /**
     * 我关注的用户
     * @return mixed
     */
    public function myCollect($param)
    {
        return (new RelationUserCollect())->myCollect($param['page'] ?? 1, $param['row'] ?? 10);
    }

    /**
     * 关注我的
     * @param $param
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collectMe($param)
    {
        return (new RelationUserCollect())->collectMe($param['page'] ?? 1, $param['row'] ?? 10);
    }


    /**
     * 用户收藏的文章
     * @param $param
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function articleCollectList($param)
    {
        $param['ids'] = Db::table('relation_article_collect')
            ->where('user_id', user_info('id'))
            ->page($param['page'] ?? 0, $param['row'] ?? 10)
            ->column('article_id');
        if ($param['ids']) {
            return $this->list($param);
        }
        return [];
    }


    /**
     * 分享数 + 1
     * @param $id
     * @return int|true
     * @throws \think\Exception
     */
    public function share($id)
    {
        return $this->where('id', $id)->setInc('share_num');
    }

}