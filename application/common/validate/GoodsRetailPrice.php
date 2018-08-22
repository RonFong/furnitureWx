<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/21 
// +----------------------------------------------------------------------


namespace app\common\validate;

use app\api\model\User;
use think\Db;

class GoodsRetailPrice extends BaseValidate
{
    protected $rule = [
        'ratio'         => 'require|elt:0|checkUser',
        'amount'        => 'require|elt:0|checkUser',
        'goods_id'      => 'require|egt:1|number',
    ];

    protected $message = [
        'ratio.require'        => '增幅比例必填',
        'ratio.gt'             => '不能小于等于0',
    ];

    protected $scene = [
        'global_ratio'   => [
            'ratio',
        ],
        'goods_amount'  => [
            'amount',
            'goods_id'
        ]
    ];


    protected function checkUser($value)
    {
        if (user_info('type') != 2) {
            return '用户类型错误';
        }
        return true;
    }

}