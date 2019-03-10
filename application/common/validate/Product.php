<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/27 
// +----------------------------------------------------------------------


namespace app\common\validate;


use think\Db;
use app\common\model\FactoryProductClassify;

class Product extends BaseValidate
{
    protected $rule = [
        'id'                => 'require',
        'classify_id'       => 'require',
        'name'              => 'require',
        'model'             => 'require',
        'texture'           => 'require',
        'style'             => 'require',
        'function'          => 'require',
        'size'              => 'require',
        'colors'            => 'require|checkColor',
        'details'           => 'require|checkDetails',
        'page'              => 'require|number',
        'row'               => 'require|number',
        'product_id'        => 'require|isAdminUser',
        'sort_action'       => 'require|in:inc,dec'
    ];


    protected $message = [
        'classify_id.require'       => '请选择产品分类',
        'name.require'              => '请填写产品名',
        'model.require'             => '请填写产品型号',
        'texture.require'           => '请填写产品材质',
        'style.require'             => '请填写产品风格',
        'function.require'          => '请填写产品风格',
        'size.require'              => '请填写产品尺寸',
        'colors.require'            => '请至少填写一个颜色信息',
        'details.require'           => '请编辑产品详情',
    ];

    protected $scene = [
        'create'    => [
            'classify_id',
            'name',
            'model',
            'texture',
            'style',
            'function',
            'size',
            'colors',
            'details',
        ],
        'update'    => [
            'id',
            'classify_id',
            'name',
            'model',
            'texture',
            'style',
            'function',
            'size',
            'colors',
            'details',
        ],
        'getListByClassify'     => [
            'classify_id',
            'page',
            'row'
        ],
        'delProduct'        => [
            'product_id'
        ],
        'changeClassify'    => [
            'classify_id'   => 'require|classifyExist',
            'product_id',
        ],
        'sort'              => [
            'product_id',
            'sort_action',
        ],
        'info'  => [
            'product_id'    => 'require'
        ],
    ];

    /**
     * 检查 colors 数据
     * @param $value
     * @return bool|string
     */
    protected function checkColor($value)
    {
        if (!is_array($value)) {
            return 'colors数据格式不正确';
        }
        foreach ($value as $v) {
            if (!is_array($v)) {
                return 'colors数据中子元素格式不正确';
            }
            if (empty($v['color']) || empty($v['img']) || empty($v['prices'])) {
                return json_encode($v) . '中不能有空值';
            }
            foreach ($v['prices'] as $vv) {
                if (empty($vv['configure']) || empty($vv['trade_price'])) {
                    return json_encode($vv) . '中不能有空值';
                }
            }
        }
        return true;
    }

    /**
     * 校验产品详情
     * @param $value
     * @return bool|string
     */
    protected function checkDetails($value)
    {
        if (!is_array($value)) {
            return 'details 元素数据格式不正确';
        }
        foreach ($value as $v) {
            if (empty($v['type']) || empty($v['content'])) {
                return json_encode($v) . '中不能有空值';
            }
            if (!in_array($v['type'], ['text', 'img'])) {
                return json_encode($v) . 'type 值不正确';
            }
        }
        return true;
    }

    /**
     * 删除校验
     * @param $value
     * @return bool|string
     */
    protected function isAdminUser($value)
    {
        $factoryId = Db::table('product')->where('id', $value)->value('factory_id');
        if (!$factoryId) {
            return '此产品不存在';
        }
        if (user_info('group_id') != $factoryId) {
            return '非本厂家用户，不可删除产品';
        }
        return true;
    }

    /**
     * 判断分类是否存在
     * @param $value
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function classifyExist($value)
    {
        $classify = (new FactoryProductClassify())->where('factory_id', user_info('group_id'))->where('id', $value)->find();
        if (!$classify) {
            return '此分类不存在';
        }
        return true;
    }
}