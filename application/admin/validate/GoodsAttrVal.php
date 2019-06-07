<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/6/6 
// +----------------------------------------------------------------------


namespace app\admin\validate;


use think\Validate;

class GoodsAttrVal extends Validate
{
    /*字段规则*/
    protected $rule = [
        'enum_name' => 'require|uniqueEnumName',
        'attr_id'   => 'require'
    ];

    /*返回错误信息*/
    protected $message = [

    ];

    protected $scene = [

    ];

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function uniqueEnumName($value, $rule, $data)
    {
        $info = (new \app\common\model\GoodsAttrVal())->where(['enum_name' => $value, 'attr_id' => $data['attr_id']])->find();
        if (!$info) {
            return true;
        }
        if (empty($data['id']) || $info['id'] != $data['id']) {
            return '此属性枚举值已存在';
        }
        return true;
    }
}