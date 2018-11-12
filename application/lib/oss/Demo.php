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

namespace app\lib\oss;

class Demo
{
    public function __construct()
    {

    }

    /**
     * 上传音频
     * @return bool
     */
    public function uploadAudio()
    {
        return 'https://www.7qiaoban.cn/multimedia/xxxxxxxxxxxx.mp3';
    }

    /**
     * 上传视频
     * @return bool
     */
    public function uploadVideo()
    {
        return 'https://www.7qiaoban.cn/multimedia/xxxxxxxxxxxx.mp4';
    }

    /**
     * 删除
     */
    public function delete()
    {
        return true;
    }
}