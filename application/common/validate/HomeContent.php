<?php
namespace app\common\validate;

/**
 * 首页图文
 * Class HomeContent
 * @package app\common\validate
 */
class HomeContent extends BaseValidate
{
    protected $rule = [
        "id"            => 'require|number',
        "content"       => 'require|contentCanNotEmpty'
    ];

    protected $message = [

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

}