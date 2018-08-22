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
        $result      = [];
        $contentData = Db::query("SELECT id,music,record,music_name FROM `home_content` WHERE group_id = {$groupId} AND group_type = {$groupType}");
        if (!empty($contentData)) {
            $contentId       = $contentData[0]['id'];
            $contentItemData = Db::query("SELECT id,text,img FROM `home_content_item` WHERE content_id = {$contentId} ORDER BY sort DESC,id ASC");
            $result = $contentData[0];
            if (!empty($contentItemData)) {
                $result['items'] = $contentItemData;
            }
        }

        return $result;
    }

    public static function setCache($data)
    {
        $groupId     = $data['groupId'];
        $groupType   = $data['groupType'];
        $cacheData = Cache::get('home_content_cache_'.$groupId.'_'.$groupType);
        $cacheData = json_decode($cacheData,true);
        var_dump($cacheData);die;
        Cache::set('home_content_cache_'.$data['itemId'], $data['text']);

        return true;
    }

    public static function getCache($data)
    {

        $result = Cache::get('home_content_cache_'.$data['itemId']);

        return $result;
    }

    public static function saveContent($data)
    {
        $groupId     = $data['groupId'];
        $groupType   = $data['groupType'];
        $music       = $data['music'];
        $record      = $data['record'];
        $musicName   = $data['musicName'];
        $items       = json_decode($data['items'],true);
        Db::query("UPDATE `home_content` SET music='{$music}',record='{$record}',music_name='{$musicName}' WHERE group_id = {$groupId} AND group_type = {$groupType}");

        if(!empty($items)){
            foreach ($items AS $key=>$value){
                $itemId = $value['id'];
                $text   = $value['text'];
                $img    = $value['img'];
                Db::query("UPDATE `home_content_item` SET text='{$text}',img='{$img}' WHERE id = {$itemId}");
            }
        }

        Cache::set('home_content_cache_'.$groupId.'_'.$groupType, json_encode($data));

        return true;
    }
}