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

class Oss
{
    /**
     * 建议您创建并使用RAM账号进行API访问或日常运维，请登录 https://ram.console.aliyun.com 创建RAM账号。
     * @var string
     */
    private $accessKeyId = "LTAIAaYdblbcmeSY";
    private $accessKeySecret = "gx0I7OkRGSxFgA9fKpbs00r8wkWTI1";

    /**
     * EndPoint（地域节点）
     * @var string
     */
    private $endpoint = "http://oss-cn-shenzhen.aliyuncs.com";

    /**
     * 存储空间名称
     * @var string
     */
    private $bucket = "api-multimedia";

    /**
     * 错误提示
     * @var string
     */
    private $error = '';

    private $request;

    /**
     * 构造函数
     * Oss constructor.
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
        $object = 'audio/' . date('Y-m-d') . '/' . time() . rand(100, 999) . strrchr($file_info['name'], '.');// 文件名称
        $filePath = $file_info['tmp_name'];//本地文件路径

        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $res = $ossClient->uploadFile($this->bucket, $object, $filePath);
        } catch (OssException $e) {
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
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error = '上传数据为空';
            return false;
        }

        $file_info = $file->getInfo();
        $object = 'video/' . date('Y-m-d') . '/' . time() . rand(100, 999) . strrchr($file_info['name'], '.');// 文件名称
        $filePath = $file_info['tmp_name'];//本地文件路径

        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $res = $ossClient->uploadFile($this->bucket, $object, $filePath);
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return $res['oss-request-url'] ?? '';
    }

    /**
     * 删除
     * @param $object
     * @return bool
     */
    public function delete($object = '')
    {
        //去掉域名部分
        if (strpos($object,'.com/') !==false) {
            $object = substr($object,strpos($object,'.com/')+5);
        }
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $ossClient->deleteObject($this->bucket, $object);
        } catch (OssException $e) {
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