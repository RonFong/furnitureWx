<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/2/21 
// +----------------------------------------------------------------------
namespace app\common\validate;

use app\lib\baiduAI\ContentCensor;
use app\lib\exception\BaseException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate {

    /**
     * 参数校验统一入口方法
     * @param string $scene 场景
     * @param array $rules  规则
     * @return bool             通过校验
     * @throws BaseException
     */
    public function goCheck($scene = '', $rules = [])
    {

        $request = Request::instance();
        $result  = $this->batch()->check($request->param(), $rules, $scene);
        if (!$result) {
            $this->error(['code' => 400, 'msg' => array_shift($this->error), 'errorCode' => 1000]);
        } else {
            return true;
        }
    }

    /**
     * 自定义异常
     * @param array $response 异常信息  [ 'http状态码’, '错误提示', '错误码' ]
     * @throws BaseException
     */
    public function error($response = [])
    {

        throw new BaseException([
            'state'      => 0,
            'code'       => $response['code'],
            'msg'        => $response['msg'],
            'error_code' => $response['errorCode'],
        ]);
    }


    /***********   公共规则 ************/
    /**
     * 校验手机号
     * @param $value
     * @return bool|string
     */
    protected function isPhoneNo($value)
    {

        $rule   = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return '手机号格式错误';
        }
    }

    /**
     * 使用 group_id 字段时， group_type 也必须有值
     * @return bool|string
     */
    protected function groupTypeExits($value, $rule, $data)
    {

        if (!array_key_exists('group_type', $data) || empty($data['group_type'])) {
            return 'group_type 不能为空';
        }

        return true;
    }

    /**
     * 百度AI文本审核
     * 批量审核当前 提交的文本内容
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \Exception
     */
    protected function textCensor($value, $rule, $data)
    {
        if (!empty($data)) {
            $contents = [];
            array_walk_recursive($data, function($value) use (&$contents) {
                if (is_string($value) && strlen($value) > 1 && $value !== 'v1' && $value !== 'v2')
                    array_push($contents, $value);
            });
            if (!empty($contents)) {
                $result = ContentCensor::text($contents);
                if ($result['state'] == 1) {
                    return '文本中有违禁内容，请修改后再提交';
                }
            }
        }
        return true;
    }

    /**
     * 百度AI文本审核
     * 审核单个文本
     * @param $value
     * @return bool|string
     * @throws \Exception
     */
    protected function aTextCensor($value)
    {
        if (!empty($value)) {
            $result = ContentCensor::text($value);
            if ($result['state'] == 1) {
                return '文本中有违禁内容，请修改后再提交';
            }
        }
        return true;
    }

    /**
     * 百度AI图片审核
     * 审核当前提交的所有图片
     * @return bool|string
     */
    protected function imgCensor()
    {
        $files = Request::instance()->file();
        if (!empty($files)) {
            $images = [];
            array_walk_recursive($files, function($value) use (&$images) {
                if (strpos($value->getInfo()['type'], 'image') !== false)
                    array_push($images, $value);
            });
            if (!empty($images)) {
                foreach ($images as $img) {
                    $result = ContentCensor::img($img->getPathname());
                    if ($result['state'] == 1)
                        return '有违禁图片，请更改后再提交';
                }
            }
        }
        return true;
    }

}