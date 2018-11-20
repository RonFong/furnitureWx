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
use app\common\model\UserLocation;
use app\common\validate\BaseValidate;
use think\Db;

class Article extends CoreArticle
{

    /**
     * 显示范围
     * @var int
     */
    private $distance = 100;

    /**
     * 在列表中，显示的图片/视频张数
     * @var int
     */
    private $imgNum = 9;

    /**
     * 默认显示评论数
     * @var int
     */
    protected $commentRow = 10;

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
            $location = UserLocation::get(user_info('id'));
            $param['lng'] = $location->lng;
            $param['lat'] = $location->lat;
            $this->save($param);
            foreach ($param['content'] as $k => $v) {
                if (!empty($v['text']) || !empty($v['img']) || !empty($v['video'])) {
                    $v['article_id'] = $this->id;
                    $v['sort'] = $k;
                    if (array_key_exists('style', $v) && !empty($v['style'])) {
                        $v['style'] = json_encode($v['style']);
                    }
                    $itemResult = (new ArticleContent())->save($v);
                    if (!$itemResult) {
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

        $page = $param['page'] ?? 0;
        $row = $param['row'] ?? 10;

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
        $location = UserLocation::get(user_info('id'));
        $sql = "select {$field} from (
                select *,(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$location->lng}-lng)/360),2)+COS(PI()*33.07078170776367/180)* COS(lat * PI()/180)*POW(SIN(PI()*({$location->lat}-lat)/360),2)))) as distance 
                from `article` 
                where {$where}) as s 
                where s.distance <= {$this->distance}
                order by {$order} limit {$page}, {$row}";
        $list = Db::query($sql);
        foreach ($list as $k => $v) {
            $list[$k]['classify_name'] = Db::table('article_classify')->where('id', $v['classify_id'])->value('classify_name');
            unset($list[$k]['classify_id']);
            $list[$k]['distance'] = $v['distance'] >= 1 ? round($v['distance'], 1) . '公里' : round($v['distance'], 2) . '米';
            $user = User::get($v['user_id']);
            $list[$k]['user_name'] = $user->user_name;
            $list[$k]['avatar'] = $user->avatar;
            $list[$k]['collect_num'] = Db::table('relation_article_collect')->where('article_id', $v['id'])->count();
            $list[$k]['great_num'] = Db::table('relation_article_great')->where('article_id', $v['id'])->count();
            $list[$k]['comment_num'] = Db::table('article_comment')
                ->where(['article_id' => $v['id'], 'parent_id' => 0, 'state' => 1])
                ->where('delete_time is null')
                ->count();
            $list[$k]['create_time'] = timeFormatForHumans($v['create_time']);
            $list[$k]['content'] = $this->getArticleContentOnList($v['id']);
        }
        return $list;
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
                ->field('type, img, img_thumb, video')
                ->order('sort');
        });
        $imgList = [];
        $num = 0;
        foreach ($content as $v) {
            if ($num >= $this->imgNum) {
                break;
            }
            if ($v['type'] != 1) {
                array_push($imgList, $v);
                $num ++;
            }
        }
        return $imgList;
    }


    /**
     * 文章详情
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function details($id)
    {
       $data = self::get(function ($query) use ($id) {
           $query->where('id', $id)
               ->field('id, user_id, classify_id as classify_name, title, music, read_num, share_num, id as comment_num, create_time');
       })->toArray();

       $user = User::get($data['user_id']);
       $data['user_name'] = $user->user_name;
       $data['avatar'] = $user->avatar;
       $data['create_time'] = timeFormatForHumans(strtotime($data['create_time']));

       $data['content'] = ArticleContent::all(function ($query) use ($id) {
           $query->where('article_id', $id)
               ->where('delete_time is null')
               ->field('delete_time', true)
               ->order('sort');
       });
       $data['comment'] = (new ArticleComment())->getComments($id, 0, 5);
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