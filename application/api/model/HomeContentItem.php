<?php
namespace app\api\model;

use app\common\model\HomeContentItem as CoreHomeContentItem;
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
}