<?php
namespace app\common\validate;

use think\Db;

/**
 * 首页图文
 * Class HomeContent
 * @package app\common\validate
 */
class HomeContent extends BaseValidate
{
    protected $rule = [
        "id"            => 'require|number|isExist',
        "content"       => 'require|contentCanNotEmpty|isRepetition'
    ];

    protected $message = [
        "content.require"   => '请填写图文内容'
    ];

    protected $scene = [
        'create'    => [
            'content'
        ],
        'update' => [
            'id',
            'content'
        ]
    ];

    protected function contentCanNotEmpty($value)
    {
        if (empty($value)) {
            return '请填写图文内容';
        }
        return true;
    }


    protected function isRepetition($value, $rule, $data)
    {
        if (!array_key_exists('id', $data) || empty($data['id'])) {
            if (Db::table('home_content')->where(['group_id' => user_info('group_id'), 'type' => user_info('type')])->find()) {
                return '首页图文内容已存在';
            }
        }
        return true;
    }

    protected function isExist($value)
    {
        if (!Db::table('home_content')->where('id', $value)->find()) {
            return '数据不存在,无法更新';
        }
        return true;
    }

}