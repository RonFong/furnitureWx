<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\common\validate;


use think\Db;

class FactoryIntro extends BaseValidate
{
    protected $rule = [
        'factory_id'        => 'require',
        'content'           => 'require',
    ];

    protected $message = [

    ];

    protected $scene = [
        'createIntro' => [
            'factory_id'    => 'require|only',
            'content',
        ],
        'updateIntro'  => [
            'factory_id'    => 'require',
            'content',
        ],
    ];

    /**
     * 是否已有简介
     * @param $value
     * @return bool|string
     */
    protected function only($value)
    {
        if ($value != user_info('group_id')) {
            return 'factory_id与当前用户所属厂家不符';
        }
        $result = Db::table('factory_intro')->where('factory_id', $value)->find();
        if ($result) {
            return '厂家简介已存在，不能重复新建';
        }
        return true;
    }
}