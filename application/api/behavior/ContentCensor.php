<?php
// +----------------------------------------------------------------------
// | Author: Octopus
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/20 15:09
// +----------------------------------------------------------------------

namespace app\api\behavior;


use think\Request;

class ContentCensor
{
    public function run()
    {
        try {
            $param = json_decode(Request::instance()->param(), true, JSON_UNESCAPED_UNICODE);
            //img 信息无需审核
            $imgKey = ['img', 'img_thumb_small', 'img_thumb_large'];

            $joinStr = function ($param, & $allStr) use (& $joinStr, $imgKey) {
                foreach ($param as $k => $v) {
                    if (is_array($v)) {
                        $joinStr($v, $allStr);
                    } else {
                        if (!in_array($k, $imgKey)) {
                            $allStr .= $v;
                        }
                    }
                }
                return $allStr;
            };

            $allStr = '';
            $allStr = $joinStr($param, $allStr);
            $result = \app\lib\baiduAI\ContentCensor::text($allStr);

            if ($result['state'] == 1 && $result['hitTag'] != 4) {
                exception('您提交的内容中包含'.$result['msg'].'信息，请修改后提交');
            }
        } catch (\Exception $e) {
            die(json_encode(['state' => 0, 'errorCode' => 1003, 'msg' => $e->getMessage()]));
        }
    }
}