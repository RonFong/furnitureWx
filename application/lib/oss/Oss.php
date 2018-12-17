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

use app\common\validate\BaseValidate;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Core\OssUtil;
use think\Image;
use think\Request;

class Oss
{
    private $currentConfig = [];


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
     * @return array|bool
     * @throws \app\lib\exception\BaseException
     */
    public function image($type)
    {
        try {
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
            $imgPath = $this->simpleUpload();
            if (!$imgPath) {
                exception('图片上传失败');
            }
            return [
                'img'               =>  $imgPath,
                'img_thumb_small'   => $smallSize == false ? $imgPath :  $imgPath . $smallSize,
                'img_thumb_large'   => $largeSize == false ? $imgPath : $imgPath . $largeSize
            ];
        } catch (\Exception $e) {
            (new BaseValidate())->error($e);
        }
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
        $sizeConfig = config('image')[$type];
        $wRatio = $sizeConfig[$size]['w']['ratio'];
        $wValue = $sizeConfig[$size]['w']['value'];
        $hRatio = $sizeConfig[$size]['h']['ratio'];
        $hValue = $sizeConfig[$size]['h']['value'];

        $width = $imgInfo->width();
        $height = $imgInfo->height();

        // 等比缩放
        if ($wRatio == 0) {
            if ($width <= $wValue) {
                //原图小于等于所设尺寸，直接使用原图，不生成缩略图
                return false;
            } else {
                $w = $wValue;
                $h = round($w / $width * $height);
            }
            $resizeSize = "w_$w";
            $cropSize = "h_$h";

            //取宽高值中与相对设定尺寸较小的边来作为缩放基数
        } elseif ($width / $wValue <= $height / $hValue) {
            if ($width < $wValue) {
                $w = $width;        //小于标准宽度，不对宽度进行缩放
            } else {
                $w = $wValue;    //大于标准宽度，则缩放至标准宽度
            }
            $resizeSize = "w_$w";
            $h = round($w / $wRatio * $hRatio);
            $cropSize = "h_$h";

        } else {
            if ($height < $hValue) {
                $h = $height;
            } else {
                $h = $hValue;
            }
            $resizeSize = "h_$h";
            $w = round($h / $hRatio * $wRatio);
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
