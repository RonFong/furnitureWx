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

use think\Image;
use app\common\model\Article;
use app\common\model\ArticleContent;

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
    protected $mainModel;

    /**
     * 当前图文内容模型
     * @var string
     */
    protected $contentModel;

    /**
     * 已保存的图片地址
     * @var array
     */
    protected $imgPath = [];


    /**
     * ImageText constructor.
     * @param $mainModel   string   图文模型名
     * @param $contentModel  string   图文内容模型
     */
    public function __construct($mainModel, $contentModel)
    {
        $this->mainModel = new $mainModel();
        $this->contentModel = new $contentModel();
    }

    static public function create($data, $files)
    {
        try {
            $articleData = [
                'user_id'       => user_info('id') ?? 0,
                'classify_id'   => $data['classify_id'],
                'music'         => $data['music'] ?? ''
            ];
            foreach ($data['content'] as $k => $v) {
                if (array_key_exists('content', $files) && array_key_exists($k, $files['content'])) {
                    $image = Image::open($files['content'][$k]);
                    print_r($image->height());
                    die;
                } elseif (!array_key_exists('text', $v) || empty($v['text'])) {
                    exception('文字和图片不能都为空');
                }
            }
        } catch (\Exception $e) {

        }
        return 1;
    }


    static public function saveImg()
    {

    }
}