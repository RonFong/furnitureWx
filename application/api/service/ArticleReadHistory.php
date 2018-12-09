<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/11/22 
// +----------------------------------------------------------------------


namespace app\api\service;
use think\Db;

/**
 * 用户阅读历史  及  文章阅读量统计
 * Class ArticleReadHistory
 * @package app\api\service
 */
class ArticleReadHistory
{
    /**
     * 记录
     * @param $articleId
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function record($articleId)
    {
        $articleUserId = Db::table('article')->where('id', $articleId)->value('user_id');
        if ($articleUserId != user_info('id')) {
            $beforeReadTime = Db::table('article_read_history')
                ->where(['user_id' => user_info('id'), 'article_id' => $articleId])
                ->order('create_time desc')
                ->value('create_time') ?: 0;
            //同一用户记录阅读数的间隔时间
            if ((time() - $beforeReadTime) > config('system.read_interval_time')) {
                $data = [
                    'user_id'       => user_info('id'),
                    'article_id'    => $articleId,
                    'date'          => date('Ymd'),
                    'create_time'   => time()
                ];
                Db::table('article')->where('id', $articleId)->inc('read_num')->update();
                Db::table('article_read_history')->insert($data);
            }
        }
    }
}