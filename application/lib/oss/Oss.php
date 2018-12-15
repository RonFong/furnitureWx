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
        'local'  => [
            'accessKeyId'       =>  'LTAI0ZISMkC8V3QE',
            'accessKeySecret'   => '80mxDCVBzNwXhbvzFQE5CiZIX8uF7j',
            'endpoint'          => 'oss-cn-hangzhou.aliyuncs.com',     //地域节点  上传
            'bucket'            => 'test-api-multimedia'                        //存储空间名
        ],'test'  => [
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
        } elseif ($this->request->domain() == 'https://www.7qiaoban.cn') {
            $this->currentConfig = $this->config['test'];
        } else {
            $this->currentConfig = $this->config['local'];
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

        //        return $this->simpleUpload();
        return $this->multiUpload();
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

//        return $this->simpleUpload();
        return $this->multiUpload();
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
            $res = $ossClient->multiuploadFile($this->currentConfig['bucket'], $this->file_name, $this->file_path);
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