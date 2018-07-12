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

/**
 * 保存图片资源
 * Class Image
 * @package app\api\controller\v1
 */
class Image extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }


    public function saveTmpImg()
    {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $this->data['img'], $result)){
            $imgFile = IMAGE_PATH . "/tmp/" . md5(uniqid(microtime(true))) . "." . $result[2];
            if (file_put_contents($imgFile, base64_decode(str_replace($result[1], '', $this->data['img'])))){
                echo '新文件保存成功：', $imgFile;
            }

        }
    }
}