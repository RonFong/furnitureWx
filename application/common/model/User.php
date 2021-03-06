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

namespace app\common\model;


use traits\model\SoftDelete;

class User extends Model
{
    use SoftDelete;

    /**
     * 只读字段
     * @var array
     */
    protected $readonly = [
        'wx_openid',
        'wx_unionid'
    ];

    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wx_openid',
        'wx_unionid',
        'create_by',
        'update_time',
        'update_by',
        'delete_time'
    ];

}