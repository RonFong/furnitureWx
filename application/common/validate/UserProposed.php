<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/21 
// +----------------------------------------------------------------------


namespace app\common\validate;


use think\Db;

class UserProposed extends BaseValidate
{
    protected $rule = [
        'user_id'           => 'require|number|checkUser',
    ];

    protected $message = [
        'user_id.require'            => '用户ID不能为空',
        'user_id.number'             => '用户ID错误',
    ];

    protected $scene = [
        'proposed'   => [
            'user_id',
        ],
    ];

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function checkUser($value, $rule, $data)
    {
        $userType = Db::table('user')->where('id', user_info('id'))->value('type');
        if ($userType == 3) {
            return '推荐失败';
        }
        $isProposed = Db::table('user_proposed')->where('proposed_id', user_info('id'))->find();
        if ($isProposed) {
            return '已被推荐';
        }
        return true;
    }
}