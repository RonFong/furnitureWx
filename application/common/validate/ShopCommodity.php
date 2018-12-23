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
        "content"           => 'require',
        "classify_name"     => 'require|isRepetition|checkLength'
    ];

    protected $message = [
        'content.require'       => '请填写内容',
        'classify_name.require' => '请填写分类名',
    ];

    protected $scene = [
        'createCommodity'    => [
            'content',
            'classify_name',
        ],
        'updateCommodity' => [
            'id',
            'content',
            'classify_name'
        ]
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
    protected function isRepetition($value, $rule, $data)
    {
        $commodity = Db::table('shop_commodity')
            ->where(['shop_id' => user_info('group_id'), 'classify_name' => $data['classify_name']])
            ->find();
        if (empty($data['id']) && $commodity) {
            return '此分类名已存在';
        }

        if ($commodity && $data['id'] !== $commodity['id'] ) {
            return '此分类名已存在';
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

    /**
     * 校验分类名长度
     * @param $value
     * @return bool|string
     */
    protected function checkLength($value)
    {
        $strlen = strlen(trim($value));
        if ($strlen < 1) {
            return '请输入分类名';
        }
        if ($strlen > 5) {
            return '分类名不能超过5个字';
        }
        return true;
    }
}