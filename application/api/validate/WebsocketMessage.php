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

namespace app\api\validate;


use app\common\validate\WebsocketMessage as CoreWebsocketMessage;

class WebsocketMessage extends CoreWebsocketMessage
{
    protected $rule = [
        'fromId'    => 'require|number',
        'toId'      => 'require|number',
    ];


    protected $scene = [
        'logWithUser' => [
            'toId'
        ],
    ];
}