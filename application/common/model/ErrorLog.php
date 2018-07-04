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


use think\Db;
use think\Request;

/**
 * 手动记录非系统级错误日志
 * Class ErrorLog
 * @package app\common\model
 */
class ErrorLog extends Model
{
    /**
     * 错误日志
     * @param $logData array
     * @param $isSuccess int
     */
    public function saveLog(array $logData, $isSuccess)
    {
        $request = Request::instance();
        $data = [
            'request_time'      => time(),
            'method'            => $request->path(),
            'ip'                => $request->ip(1) ?? 0,
            'params'            => $logData,
            'user_agent'        => $request->header('user-agent') ?? '',
            'is_success'        => $isSuccess
        ];
        Db::table('errorLog')->insert($data);
    }
}