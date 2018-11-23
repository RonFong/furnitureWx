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

use OSS\OssClient;
use OSS\Core\OssException;
use think\Request;

class Demo
{
    /**
     * 建议您创建并使用RAM账号进行API访问或日常运维，请登录 https://ram.console.aliyun.com 创建RAM账号。
     * @var string
     */
    private $accessKeyId = "LTAIFFUNplwfZD27";
    private $accessKeySecret = "XUtgBieD4jxsIByaIvdaI4LWfoU9Th";

    /**
     * EndPoint（地域节点）
     * @var string
     */
    private $endpoint = "http://oss-cn-beijing.aliyuncs.com";

    /**
     * 存储空间名称
     * @var string
     */
    private $bucket= "dlx-0";

    /**
     * 错误提示
     * @var string
     */
    private $error = '';

    private $request;
    /**
     * 构造函数
     * Demo constructor.
     */
    public function __construct()
    {
        $this->request = Request::instance();
    }

    /**
     * 上传音频
     * @return bool
     */
    public function uploadAudio()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error = '上传数据为空';
            return false;
        }

        $file_info = $file->getInfo();
        $object = time(). rand(100, 999). strrchr($file_info['name'], '.');// 文件名称
        $filePath = $file_info['tmp_name'];//本地文件路径

        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $res = $ossClient->uploadFile($this->bucket, $object, $filePath);
        } catch(OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return $res['oss-request-url'] ?? '';
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
        $object = "1542944401191.jpg";
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $ossClient->deleteObject($this->bucket, $object);
        } catch(OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}