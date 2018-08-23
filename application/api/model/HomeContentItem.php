<?php
namespace app\api\model;

use app\common\model\HomeContentItem as CoreHomeContentItem;
use think\Cache;
use think\Db;

class HomeContentItem extends CoreHomeContentItem
{

    public static function getContentItem($data)
    {

        $contentItemData = Db::query("SELECT * FROM `home_content_item` WHERE id = {$data['itemId']}");

        return $contentItemData[0];
    }

    public static function getContent($data)
    {

        $groupId     = $data['groupId'];
        $groupType   = $data['groupType'];
        $result      = [
            'id'         => '',
            'music'      => '',
            'record'     => '',
            'music_name' => '',
            'items'      => [],
        ];
        $cacheData = $result;
        $contentData = Db::query("SELECT id,music,record,music_name FROM `home_content` WHERE group_id = {$groupId} AND group_type = {$groupType}");
        if (!empty($contentData)) {
            $contentId       = $contentData[0]['id'];
            $contentItemData = Db::query("SELECT id,text,img FROM `home_content_item` WHERE content_id = {$contentId} ORDER BY sort DESC,id ASC");
            $result          = $contentData[0];
            $result['items'] = [];
            if (!empty($contentItemData)) {
                $result['items'] = $contentItemData;
            }
            $cacheData = [
                'id'         => $contentData[0]['id'],
                'music'      => $contentData[0]['music'],
                'record'     => $contentData[0]['record'],
                'music_name' => $contentData[0]['music_name'],
                'items'      => $result['items'],
            ];
        }
        Cache::set('home_content_cache_' . $groupId . '_' . $groupType, json_encode($cacheData));

        return $result;
    }

    public static function setCache($data)
    {

        $groupId   = $data['groupId'];
        $groupType = $data['groupType'];
        $cacheData = Cache::get('home_content_cache_' . $groupId . '_' . $groupType);
        if (empty($cacheData)) {
            $cacheData = [
                'music'      => '',
                'record'     => '',
                'music_name' => '',
                'items'      => [
                    $data['itemKey'] => [
                        'id'   => '',
                        'text' => $data['text'],
                        'img'  => $data['img'],
                    ],
                ],
            ];
        } else {
            switch ($data['type']) {
                case 1:
                    if (!empty($cacheData['items'])) {
                        foreach ($cacheData['items'] AS $key => &$value) {
                            if ($key == $data['itemKey']) {
                                if($data['text'] !== false){
                                    $value['text'] = $data['text'];
                                }
                                if($data['img'] !== false){
                                    $value['img'] = $data['img'];
                                }
                                break;
                            }
                        }
                    }
                    if($data['music'] !== false){
                        $cacheData['music'] = $data['music'];
                    }
                    if($data['musicName'] !== false){
                        $cacheData['music_name'] = $data['musicName'];
                    }
                    break;
                case 2:
                    $pushData = [
                        'id'   => '',
                        'text' => '',
                        'img'  => '',
                    ];
                    array_push($cacheData['items'], $pushData);
                    break;
                case 3:
                    array_splice($cacheData['items'], $data['itemKey'], 1);
                    break;
            }
        }
        Cache::set('home_content_cache_' . $groupId . '_' . $groupType, json_encode($cacheData));

        return true;
    }

    public static function getCache($data)
    {

        $groupId   = $data['groupId'];
        $groupType = $data['groupType'];
        $itemKey   = $data['itemKey'];
        $result    = Cache::get('home_content_cache_' . $groupId . '_' . $groupType);
        switch ($data['type']) {
            case 1:
                break;
            case 2:
                $result = $result['items'][$itemKey];
                break;
        }

        return $result;
    }

    public static function saveContent($data)
    {

        $groupId     = $data['groupId'];
        $groupType   = $data['groupType'];
        $music       = $data['music'];
        $record      = $data['record'];
        $musicName   = $data['musicName'];
        $items       = json_decode($data['items'], true);
        $contentData = Db::query("SELECT id,music,record,music_name FROM `home_content` WHERE group_id = {$groupId} AND group_type = {$groupType}");
        if (!empty($contentData)) {
            Db::execute("UPDATE `home_content` SET music='{$music}',record='{$record}',music_name='{$musicName}' WHERE group_id = {$groupId} AND group_type = {$groupType}");
            $contentId = $contentData[0]['id'];
        } else {
            $contentId = Db::execute("INSERT INTO `home_content`(group_id,group_type,music,record,music_name) values('{$groupId}','{$groupType}','{$music}','{$record}','{$musicName}')");
        }
        if (!empty($items)) {
            foreach ($items AS $key => &$value) {
                $itemId = $value['id'];
                $text   = $value['text'];
                $img    = $value['img'];
                if (empty($itemId)) {
                    $itemId      = Db::execute("INSERT INTO `home_content_item`(content_id,text,sort,img) values('{$contentId}','{$text}',0,'{$img}')");
                    $value['id'] = $itemId;
                } else {
                    Db::execute("UPDATE `home_content_item` SET text='{$text}',img='{$img}' WHERE id = {$itemId}");
                }
                unset($items[$key]['format_text']);
            }
        }
        $cacheData = [
            'music'      => $music,
            'record'     => $record,
            'music_name' => $musicName,
            'items'      => $items,
        ];
        Cache::set('home_content_cache_' . $groupId . '_' . $groupType, json_encode($cacheData));

        return true;
    }
}