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
     * 异常和错误处理
     * @param mixed $response 参数为数组时：自定义异常  参数为对象时：系统错误
     * @throws BaseException
     */
    public function error($response)
    {
        throw new BaseException([
            'state'         => 0,
            'code'          => is_array($response) ? $response['code'] : 500,
            'msg'           => is_array($response) ? $response['msg'] : $response->getMessage(),
            'error_code'    => is_array($response) ? $response['errorCode'] : 999
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

        $rule   = '^1([2-9])[0-9]\d{8}$^';
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
     * 百度AI图片审核   （base64  格式）
     * 审核当前限定规则的图片
     * @param $value string 图片的base4 编码
     * @return bool|string
     */
    protected function imgBase64Censor($value)
    {
        $result = ContentCensor::img($value);
        if ($result['state'] == 1)
            return '有违禁图片，请更改后再提交';
    }

    /**
     * 百度AI图片审核   （file  格式）
     * 审核当前提交的所有图片
     * @return bool|string
     */
    protected function imgFileCensor()
    {
        try {
            $files = Request::instance()->file();
        } catch (\Exception $e) {
            $files = $_FILES;
        }
        if (!empty($files)) {
            $images = [];
            if (is_object(current($files))) {
                array_walk_recursive($files, function($value) use (&$images) {
                    if (strpos($value->getInfo()['type'], 'image') !== false)
                        array_push($images, $value);
                });
            } else {
                foreach ($files as $k => $v) {
                    foreach ($v['tmp_name'] as $kk => $vv) {
                        if (array_key_exists('img', $vv))
                            array_push($images, $vv['img']);
                    }
                }
            }
            if (!empty($images)) {
                foreach ($images as $img) {
                    $tmpPath = is_object($img) ? $img->getPathname() : $img;
                    $result = ContentCensor::img($tmpPath);
                    if ($result['state'] == 1)
                        return '有违禁图片，请更改后再提交';
                }
            }
        }
        return true;
    }

    /**
     * 页码和每页条目数的统一校验
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkPageAndRow($value, $rule, $data)
    {
        if (array_key_exists('page', $data)) {
            if (!is_numeric($data['page']))
                return '页码只能是数字';
            if ($data['page'] < 0)
                return '页码不能小于0';
        }
        if (array_key_exists('row', $data)) {
            if (!is_numeric($data['row']))
                return '条目数只能是数字';
            if ($data['row'] < 0)
                return '条目数不能小于0';
        }
        return true;
    }

}