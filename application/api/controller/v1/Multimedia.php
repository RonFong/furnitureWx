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

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\lib\oss\Demo;
use think\Request;

/**
 * 多媒体文件上传
 * Class Multimedia
 * @package app\api\controller\v1
 */
class Multimedia extends BaseController
{
    protected $ossServer;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->ossServer = new Demo();
    }

    /**
     * 上传音频
     * @return \think\response\Json
     */
    public function uploadAudio()
    {
        $this->result['data'] = ['url' => $this->ossServer->uploadAudio()];
        return json($this->result, 200);
    }

    /**
     * 上传视频
     * @return \think\response\Json
     */
    public function uploadVideo()
    {
        $this->result['data'] = ['url' => $this->ossServer->uploadVideo()];
        return json($this->result, 200);
    }
}