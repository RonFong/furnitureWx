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

namespace app\api\service;

use think\Db;

/**
 * 图文
 * Class ImageText
 * @package app\api\service
 */
class ImageText
{
    /**
     * 当前图文模型
     * @var string
     */
    protected static $mainModel;

    /**
     * 当前图文内容模型
     * @var string
     */
    protected static $contentModel;


    /**
     * 图片保存目标文件夹名
     * @var string
     */
    protected static $folder ;


    /**
     * ImageText constructor.
     * @param $mainModel   string       图文模型名
     * @param $contentModel  string     图文内容模型
     * @param $folder  string           图片保存目标文件夹名  (static/img 文件夹下的文件夹名)
     */
    public function __construct($mainModel, $contentModel, $folder)
    {
        self::$mainModel = $mainModel;
        self::$contentModel = $contentModel;
        self::$folder = '/' . $folder . '/';
    }

    /**
     * 图文信息保存及更新
     * @param $data   array   图文数据
     * [
     * 'title' => '',
     * 'classify_id' => '',
     * 'music' => '',
     * 'content' => [
     *      [
     *          'sort'  => 1,
     *          'img'   => '',
     *          'text'  => ''
     *      ],
     *      [
     *          'sort'  => 2,
     *          'img'   => '',
     *          'text'  => ''
     *      ]
     * ]
     * @return array
     */
    public static function write($data)
    {
        //开启事务
        Db::startTrans();
        try {
            $contentData = $data['content'];
            unset($data['content']);

            //文章主信息
            $articleData = $data;
            $articleData['id'] = user_info('id') ?? 0;      //user_id  必须， 暂缺失

            //有传id则为更新操作
            if (array_key_exists('id', $data) && is_numeric($data['id']))
                $articleData['id'] = $data['id'];

            //保存文章信息
            self::$mainModel->save($articleData);

            //获取文章ID
            $articleID = self::$mainModel->id;

            //保存文章内容块
            $contentID = [];
            foreach ($contentData as $v) {
                $v['article_id'] = $articleID;

                if (array_key_exists('img', $v) && !empty($v['img'])) {
                    //转存临时图片到当前模块指定文件夹
                    $newImgPath = move_tmp_img($v['img'], self::$folder);
                    if ($newImgPath == false){
                        exception('图片转储失败');
                    }
                    $v['img'] = $newImgPath;
                }

                $model = self::initContentModel();
                $model->save($v);

                //记录所保存的内容块id
                array_push($contentID, $model->id);
            }

            //若此次为更新操作，则已存在且不包含在更新数据中的数据，应删除
            if (array_key_exists('id', $articleData)) {
                $popData = self::$contentModel->where('id', 'not in', $contentID)->column('img', 'id');
                if ($popData)
                    self::$contentModel->delete(array_keys($popData));
                    //删除图片
                    if ($popImg = array_filter($popData)) {
                        unlink_img($popImg);
                    }
            }
            //提交事务
            Db::commit();

        } catch (\Exception $e) {
            //事务回滚
            Db::rollback();
            return ['state' => false, 'msg' => $e->getMessage()];
        }
        return ['state' => true, 'id' => $articleID];
    }

    /**
     * 初始化文章内容模型，避免在循环写入中混淆数据
     * @return string
     */
    private static function initContentModel()
    {
        return clone self::$contentModel;
    }

}