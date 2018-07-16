<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/12 
// +----------------------------------------------------------------------


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use think\Request;
use think\Image as ThinkImage;

/**
 * 保存图片资源
 * Class Image
 * @package app\api\controller\v1
 */
class Image extends BaseController
{
    /**
     * 临时图片存储地址
     * @var string
     */
    protected $imgPath;

    /**
     * 前端图片调用地址
     * @var string
     */
    protected $viewImgPath;

    /**
     * 缩略图后缀
     * @var string
     */
    protected $thumbImgSuffix = '_thumb';

    /**
     * 缩略图尺寸
     * @var string
     */
    protected $thumbImgSize = [
        'width'     => 150,
        'height'    => 150
    ];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->imgPath = IMAGE_PATH . "/tmp/";
        $this->viewImgPath = VIEW_IMAGE_PATH . "/tmp/";
    }


    /**
     * @api {post} /v1/image/temporary  临时存储图片 (base64格式)
     * @apiGroup image
     *
     * @apiParamExample  {string} 请求参数格式：
     * {
     *      "img":"data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDA********"
     * }
     *
     * @apiSuccessExample {json} 成功时的返回：
     * {
     *      "data": "/static/img/tmp/f70cc04e10d49e0dfc8ea49029d38593.jpeg"
     * }
     */
    public function saveTmpImg()
    {
        try {
            //生成图片名
            //base64格式上传
            if (array_key_exists('img', $this->data) && $this->data['img']) {
                if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $this->data['img'], $result))
                    exception('参数错误');
                //写入到目标文件
                $imgFile = md5(uniqid(microtime(true))) . '.' . $result[2];
                file_put_contents($this->imgPath . $imgFile, base64_decode(str_replace($result[1], '', $this->data['img'])));
            } elseif ($this->files['img']) {
                //file格式上传
                $info = $this->files['img']->move($this->imgPath);
                if (!$info)
                    exception($info->getError());
                $imgFile = $info->getSaveName();
            }

            //生成后缀为 $this->thumbImgSuffix 的缩略图
            $image = ThinkImage::open($this->imgPath . $imgFile);
            if ($image->width() < $this->thumbImgSize['width'] || $image->height() < $this->thumbImgSize['height']) {
                //原图尺寸小于预定缩略图，则固定尺寸缩放裁剪
                $thumbType = ThinkImage::THUMB_FIXED;
            } else {
                //居中缩放裁剪
                $thumbType = ThinkImage::THUMB_CENTER;
            }
            //保存缩略图
            $array = explode('.', $imgFile);
            $array[count($array) - 2] = $array[count($array) - 2] . '_thumb.';
            $thumbImg = implode('', $array);
            $image->thumb($this->thumbImgSize['width'], $this->thumbImgSize['height'], $thumbType)
                ->save($this->imgPath . $thumbImg);

            $this->result['data'] = [
                'img'       => $this->viewImgPath . $imgFile,
                'img_thumb' => $this->viewImgPath . $thumbImg
            ];
        } catch (\Exception $e) {
            $code = 400;
            $this->result['state'] = 0;
            $this->result['msg'] = $e->getMessage();
        }
        return json($this->result, $code ?? 200);
    }
}