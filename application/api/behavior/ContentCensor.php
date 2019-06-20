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
            $param = Request::instance()->param();
            unset($param['version']);
            if (!empty($param)) {
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
                if (!empty($allStr)) {
                    $tmpStr = getChinese($allStr);
                    if ($tmpStr) {
                        $allStr = explode(',', $tmpStr);
                        $result = \app\lib\baiduAI\ContentCensor::text($allStr);
                        if ($result['state'] == 1) {
                            exception('内容中含 ' . $result['msg'] . ' 信息，请修改');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            die(json_encode(['state' => 0, 'errorCode' => 1003, 'msg' => $e->getMessage()]));
        }
    }
}