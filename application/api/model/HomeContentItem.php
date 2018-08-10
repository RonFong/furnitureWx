<?php
namespace app\api\model;

use app\common\model\HomeContentItem as CoreHomeContentItem;
use think\Db;

class HomeContentItem extends CoreHomeContentItem
{
    public static function getContentItem($data){
        $sql         = "SELECT * FROM `home_content_item`
                WHERE id = {$data['itemId']}
                LIMIT 1";
        $contentItem = Db::query($sql);

        return $contentItem;
    }
}