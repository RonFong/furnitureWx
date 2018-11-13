<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/11/13
// +----------------------------------------------------------------------


namespace app\common\validate;


use think\Db;

class ShopCommodity extends BaseValidate
{
    protected $rule = [
        "id"                => 'require|number|isExist',
        "name"              => 'require',
        "content"           => 'require|contentCanNotEmpty|isRepetition',
        "classify_name"     => 'require'
    ];

    protected $message = [

    ];

    protected $scene = [
        'createCommodity'    => [
            'content',
            'name',
            'classify_name',
        ],
        'updateCommodity' => [
            'id',
            'name',
            'content',
            'classify_id'
        ]
    ];

    protected function contentCanNotEmpty($value)
    {
        if (empty($value)) {
            return '请填写商品内容';
        }
        return true;
    }

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function isRepetition($value, $rule, $data)
    {
        if (!array_key_exists('id', $data) || empty($data['id'])) {
            if (Db::table('shop_commodity')->where('group_id', user_info('group_id'))->find()) {
                return '首商品内容已存在';
            }
        }
        return true;
    }

    /**
     * @param $value
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function isExist($value)
    {
        if (!Db::table('shop_commodity')->where('id', $value)->find()) {
            return '数据不存在,无法更新';
        }
        return true;
    }
}