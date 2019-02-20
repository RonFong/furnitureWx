<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/20 
// +----------------------------------------------------------------------


namespace app\common\validate;
use think\Db;

/**
 * 厂家产品分类
 * Class FactoryProductClassify
 * @package app\common\validate
 */
class FactoryProductClassify extends BaseValidate
{
    protected $rule = [
        'id'                => 'require|number|isExistId',
        'parent_id'         => 'isExistParentId',
        'classify_name'     => 'require|isUnique|isFactoryUser',
        'sort'              => 'number',
    ];

    protected $message = [
        'classify_name.require'     => '请填写分类名',
        'sort.number'               => '排序号必须是数字',
    ];

    protected $scene = [
        'create'    => [
            'classify_name'
        ],
        'edit'      => [
            'id',
            'classify_name',
            'sort'
        ],
    ];

    /**
     * 父级分类是否存在
     * @param $value
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function isExistParentId($value)
    {
        $classify = Db::table('factory_product_classify')->where('id', $value)->find();
        if (!$classify) {
            return '父级分类不存在';
        }
        if (user_info('group_id') != $classify['factory_id']) {
            return '父级分类非不属于当前门店';
        }
        return true;
    }

    /**
 * 分类名是否已存在
 * @param $value
 * @param $rule
 * @param $data
 * @return bool|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
    protected function isUnique($value, $rule, $data)
    {
        $classify = Db::table('factory_product_classify')->where('classify_name', $value)->find();
        if (($classify && !array_key_exists('id', $data)) || ($classify && $data['id'] != $classify['id'])) {
            return '此分类名已存在';
        }
        return true;
    }

    /**
     * 非厂家用户，不可添加此分类
     * @param $value
     * @return bool|string
     */
    protected function isFactoryUser($value)
    {
        if (user_info('type') != 1) {
            return '非厂家用户，不可添加此分类';
        }
        return true;
    }

    /**
     * 分类id是否存在
     * @param $value
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function isExistId($value)
    {
        $classify = Db::table('factory_product_classify')->where('id', $value)->where('delete_time is null')->find();
        if (!$classify) {
            return '该分类不存在';
        }
        if ($classify['factory_id'] != user_info('group_id')) {
            return '不能修改非当前门店的分类';
        }
        return true;
    }
}