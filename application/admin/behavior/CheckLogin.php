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

namespace app\admin\behavior;
use think\response\Redirect;
use think\exception\HttpResponseException;
use think\Request;
class CheckLogin
{
    public function run()
    {
        $request = Request::instance();
        $uid = user_info('id');
        if ($request->controller() !== 'Login' && empty($uid)) {
            $response = new Redirect('Admin/Login/index');
            $response->code(500)->remember();
            throw new HttpResponseException($response);
        }
    }
}