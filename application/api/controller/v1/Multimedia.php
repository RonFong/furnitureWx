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
use app\lib\oss\Oss;
use think\Db;
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
        $this->ossServer = new Oss();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function uploadImg()
    {
        $type = $this->request->param('type', 'default');
        $res = $this->ossServer->image($type);
        if ($res === false) {
            $this->result['state'] = 0;
            $this->result['msg'] = $this->ossServer->getError();
            return json($this->result, 200);
        }

        $this->result['data'] = ['url' => $res];
        return json($this->result, 200);
    }

    /**
     * 上传音频
     * @return \think\response\Json
     */
    public function uploadAudio()
    {
        $res = $this->ossServer->audio();
        if ($res === false) {
            $this->result['state'] = 0;
            $this->result['msg'] = $this->ossServer->getError();
            return json($this->result, 200);
        }

        $this->result['data'] = ['url' => $res];
        return json($this->result, 200);
    }

    /**
     * 上传视频
     * @return \think\response\Json
     */
    public function uploadVideo()
    {
        $res = $this->ossServer->video();
        if ($res === false) {
            $this->result['state'] = 0;
            $this->result['msg'] = $this->ossServer->getError();
            return json($this->result, 200);
        }

        $this->result['data'] = ['url' => $res];
        return json($this->result, 200);
    }

    /**
     * 删除文件
     * @return \think\response\Json
     */
    public function delete()
    {
        if (empty($this->data['table_name']) || empty($this->data['field_name']) || empty($this->data['url'])) {
            $this->result['state'] = 0;
            $this->result['msg'] = "参数错误！";
            return json($this->result, 200);
        }

        $res = $this->ossServer->delete($this->data['url']);
        if ($res !== false && !empty($this->data['id'])) {
            $pk = Db::name($this->data['table_name'])->getPk();
            Db::name($this->data['table_name'])->where($pk, $this->data['id'])->update([$this->data['field_name']=>'']);
        }
        return json($this->result, 200);

    }
}