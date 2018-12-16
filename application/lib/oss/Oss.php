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
use think\Image;
use think\Request;

class Oss
{
    private $currentConfig = [];

    //图片缩略尺寸
    const imgThumbSize = [
        //门店
        'shop'      => [
            'small'   => [           //缩略图中的小号图
                'w'     => [          //宽度尺寸
                    'ratio' => 4,     //宽高比例
                    'value' => 480    //值
                ],
                'h'     => [
                    'ratio' => 3,
                    'value' => 360
                ],
            ],
            'large'  => [           //缩略图中的大号图
                'w'     => [
                    'ratio' => 16,
                    'value' => 1080
                ],
                'h'     => [
                    'ratio' => 9,
                    'value' => 607
                ]
            ]
        ],
        //文章
        'article'   => [
            'small'  => [
                'w'     => [
                    'ratio' => 4,
                    'value' => 500
                ],
                'h'     => [
                    'ratio' => 3,
                    'value' => 375
                ]
            ],
            'large'   => [
                'w'     => [
                    'ratio' => 0,       // 等比缩放
                    'value' => 1000
                ],
                'h'     => [
                    'ratio' => 0,
                    'value' => 0
                ]
            ]
        ],
        //默认
        'default'   => [
            'small'  => [
                'w'     => [
                    'ratio' => 1,
                    'value' => 400
                ],
                'h'     => [
                    'ratio' => 1,
                    'value' => 400
                ]
            ],
            'large'  => [
                'w'     => [
                    'ratio' => 4,
                    'value' => 400
                ],
                'h'     => [
                    'ratio' => 3,
                    'value' => 300
                ]
            ]
        ]
    ];


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
        $config = config('api.oss');
        if ($this->request->domain() == 'https://www.86jj.cn') {
            $this->currentConfig = $config['online'];
        } elseif ($this->request->domain() == 'https://www.7qiaoban.cn') {
            $this->currentConfig = $config['test'];
        } else {
            $this->currentConfig = $config['local'];
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
            $this->error = '请选择要上传的音频';
            return false;
        }

        $file_info = $file->getInfo();
        $this->file_name = 'audio/' . date('Y-m-d') . '/' . time() . rand(100, 999) . strrchr($file_info['name'], '.');// 文件名称
        $this->file_path = $file_info['tmp_name'];//本地文件路径

        return $this->simpleUpload();
//        return $this->multiUpload();
    }

    /**
     * 上传视频
     * @return bool
     */
    public function video()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error = '请选择要上传的视频';
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
            $this->error = '请选择要上传的图片';
            return false;
        }

        $imgInfo = Image::open($this->request->file('file'));

        //计算缩略图宽高
        $smallSize = $this->calculateImgThumbSize($imgInfo, $type, 'small');
        $largeSize = $this->calculateImgThumbSize($imgInfo, $type, 'large');

        $file_info = $file->getInfo();
        $this->file_name = 'image/' .$type. '/'. date('Y-m-d') . '/' . time() . rand(100, 999) . strrchr($file_info['name'], '.');// 文件名称
        $this->file_path = $file_info['tmp_name'];//本地文件路径

        return [
            'img'               =>  $this->simpleUpload(),
            'img_thumb_small'   => $smallSize == false ? $this->simpleUpload() :  $this->simpleUpload() . $smallSize,
            'img_thumb_large'   => $largeSize == false ? $this->simpleUpload() : $this->simpleUpload() . $largeSize
        ];
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

    /**
     * 计算缩略图宽高值
     * @param $imgInfo   object   图片信息
     * @param $type    string 类型   shop | article | default
     * @param $size    string   尺码  small | large
     * @return string
     */
    private function calculateImgThumbSize($imgInfo, $type, $size)
    {
        $sizeConfig = self::imgThumbSize[$type];
        $width = $imgInfo->width();
        $height = $imgInfo->height();

        if ($sizeConfig[$size]['w']['ratio'] == 0) {
            // 等比缩放
            if ($width <= $sizeConfig[$size]['w']['value']) {
                //原图小于等于所设尺寸，直接使用原图，不生成缩略图
                return false;
            } else {
                $w = $sizeConfig[$size]['w']['value'];
                $h = round($w / $width * $height);
            }
            $resizeSize = "w_$w";
            $cropSize = "h_$h";

        } elseif ($width <= $height) {
            //取宽高值中较小的值来作为缩放基数
            if ($width < $sizeConfig[$size]['w']['value']) {
                $w = $width;        //小于标准宽度，不对宽度进行缩放
            } else {
                $w = $sizeConfig[$size]['w']['value'];    //大于标准宽度，则缩放至标准宽度
            }
            $resizeSize = "w_$w";
            $h = round($w / $sizeConfig[$size]['w']['ratio'] * $sizeConfig[$size]['h']['ratio']);
            $cropSize = "h_$h";
        } else {
            if ($height < $sizeConfig[$size]['h']['value']) {
                $h = $height;
            } else {
                $h = $sizeConfig[$size]['h']['value'];
            }
            $resizeSize = "h_$h";
            $w = round($h / $sizeConfig[$size]['h']['ratio'] * $sizeConfig[$size]['w']['ratio']);
            $cropSize = "w_$w";
        }
        return $this->setImgSize($resizeSize, $cropSize);
    }

    /**
     * 获取图片缩放值
     * @param $resizeSize string  缩放
     * @param $cropSize   string  裁剪
     * @return string
     */
    private function setImgSize($resizeSize, $cropSize)
    {
        return "?x-oss-process=image/resize,m_lfit,$resizeSize/crop,x_0,y_0,$cropSize,g_center";
    }


}
