<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/2/21 
// +----------------------------------------------------------------------
namespace app\common\validate;

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

}