<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2017/12/21 22:48
// +----------------------------------------------------------------------

namespace app\admin\controller;


class Index extends BaseController
{

    public function index()
    {
        return parent::index();
    }

    /**
     * 清除缓存
     */
    public function clear()
    {
        if(function_exists('opcache_reset'))
        {
            opcache_reset();
        }
        cache(null);
        array_map('unlink',glob(TEMP_PATH.DS.'*.php'));
        $this->success('清除缓存成功','index');
    }

}