<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/15 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\ArticleContent as CoreArticleContent;

class ArticleContent extends CoreArticleContent
{
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