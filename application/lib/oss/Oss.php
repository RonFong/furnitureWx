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
use OSS\Core\OssUtil;
use think\Request;

class Oss
{
    private $config = [
        'test'  => [
            'accessKeyId'       =>  'LTAI0ZISMkC8V3QE',
            'accessKeySecret'   => '80mxDCVBzNwXhbvzFQE5CiZIX8uF7j',
            'endpoint'          => 'oss-cn-hangzhou-internal.aliyuncs.com',     //地域节点  上传
            'bucket'            => 'test-api-multimedia'                        //存储空间名
        ],
        'online'    => [
            'accessKeyId'       =>  'LTAIAaYdblbcmeSY',
            'accessKeySecret'   => 'gx0I7OkRGSxFgA9fKpbs00r8wkWTI1',
            'endpoint'          => 'oss-cn-shenzhen-internal.aliyuncs.com',
            'bucket'            => 'api-multimedia'
        ]
    ];

    private $currentConfig = [];

    /**
     * 居中裁剪，宽1080*607（16:9）
     * @var string
     */
    const crop_1080 = '?x-oss-process=image/crop,x_0,y_0,w_1080,h_607,g_center';

    /**
     * 居中裁剪，宽408*306（4:3）
     * @var string
     */
    const crop_408 = '?x-oss-process=image/crop,x_0,y_0,w_408,h_306,g_center';

    /**
     * 缩放，宽500*375（4:3）
     * @var string
     */
    const resize_500 = '?x-oss-process=image/resize,m_fixed,h_375,w_500';

    /**
     * 缩放，宽1000 高auto
     * @var string
     */
    const resize_1000 = '?x-oss-process=image/resize,w_1000';

    /**
     * 文件名称
     * @var string
     */
    private $file_name = '';

    /**
     * 文件路径
     * @var string
     */
    private $file_path = '';

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
        if ($this->request->domain() == 'https://www.99jjw.cn') {
            $this->currentConfig = $this->config['online'];
        } else {
            $this->currentConfig = $this->config['test'];
        }
    }

    /**
     * 上传音频
     * @return bool
     */
    public function audio()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error = '上传数据为空';
            return false;
        }

        $file_info = $file->getInfo();
        $this->file_name = 'audio/' . date('Y-m-d') . '/' . time() . rand(100, 999) . strrchr($file_info['name'], '.');// 文件名称
        $this->file_path = $file_info['tmp_name'];//本地文件路径

        return $this->simpleUpload();
    }

    /**
     * 上传视频
     * @return bool
     */
    public function video()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error = '上传数据为空';
            return false;
        }

        $file_info = $file->getInfo();
        $this->file_name = 'video/' . date('Y-m-d') . '/' . time() . rand(100, 999) . strrchr($file_info['name'], '.');// 文件名称
        $this->file_path = $file_info['tmp_name'];//本地文件路径

        return $this->simpleUpload();
    }

    /**
     * 上传图片
     * @param $type
     * @return bool|mixed|string
     */
    public function image($type)
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error = '上传数据为空';
            return false;
        }

        $file_info = $file->getInfo();
        $this->file_name = 'image/' .$type. '/'. date('Y-m-d') . '/' . time() . rand(100, 999) . strrchr($file_info['name'], '.');// 文件名称
        $this->file_path = $file_info['tmp_name'];//本地文件路径


        return $this->simpleUpload();
    }

    /**
     * 简单上传
     */
    public function simpleUpload()
    {
        try {
            $ossClient = new OssClient($this->currentConfig['accessKeyId'], $this->currentConfig['accessKeySecret'], $this->currentConfig['endpoint']);
            $res = $ossClient->uploadFile($this->currentConfig['bucket'], $this->file_name, $this->file_path);
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $res['oss-request-url'] ? str_replace('-internal', '', $res['oss-request-url']) : '';
    }

    /**
     * 分片式上传
     */
    public function multiUpload()
    {
        try {
            $ossClient = new OssClient($this->currentConfig['accessKeyId'], $this->currentConfig['accessKeySecret'], $this->currentConfig['endpoint']);
            //初始化一个分片上传事件，获取uploadId。
            $uploadId = $ossClient->initiateMultipartUpload($this->currentConfig['bucket'], $this->file_name);
            //上传分片
            $partSize = 5 * 1024 * 1024;//单个分片大小，默认5M
            $uploadFileSize = filesize($this->file_path);
            $pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
            $responseUploadPart = array();
            $uploadPosition = 0;
            $isCheckMd5 = true;
            foreach ($pieces as $i => $piece) {
                $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
                $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
                $upOptions = array(
                    $ossClient::OSS_FILE_UPLOAD => $this->file_path,
                    $ossClient::OSS_PART_NUM => ($i + 1),
                    $ossClient::OSS_SEEK_TO => $fromPos,
                    $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
                    $ossClient::OSS_CHECK_MD5 => $isCheckMd5,
                );
                // MD5校验。
                if ($isCheckMd5) {
                    $contentMd5 = OssUtil::getMd5SumForFile($this->file_path, $fromPos, $toPos);
                    $upOptions[$ossClient::OSS_CONTENT_MD5] = $contentMd5;
                }
                $responseUploadPart[] = $ossClient->uploadPart($this->currentConfig['bucket'], $this->file_name, $uploadId, $upOptions);
            }
            // $uploadParts是由每个分片的ETag和分片号（PartNumber）组成的数组。
            $uploadParts = array();
            foreach ($responseUploadPart as $i => $eTag) {
                $uploadParts[] = array(
                    'PartNumber' => ($i + 1),
                    'ETag' => $eTag,
                );
            }

            //完成上传，OSS将把这些分片组合成一个完整的文件。
            $res = $ossClient->completeMultipartUpload($this->currentConfig['bucket'], $this->file_name, $uploadId, $uploadParts);
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return $res['oss-request-url'] ? str_replace('-internal', '', $res['oss-request-url']) : '';
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
            $ossClient = new OssClient($this->currentConfig['accessKeyId'], $this->currentConfig['accessKeySecret'], $this->currentConfig['endpoint']);
            $ossClient->deleteObject($this->currentConfig['bucket'], $object);
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