<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/27 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\Product as CoreProduct;
use app\common\validate\BaseValidate;
use think\Db;


class Product extends CoreProduct
{
    /**
     * 发布产品
     * @param $saveData
     * @return mixed
     * @throws \app\lib\exception\BaseException
     */
    public function saveData($saveData)
    {
        Db::startTrans();
        try {
            $colors = $saveData['colors'];
            unset($saveData['colors']);
            $saveData['factory_id'] = user_info('group_id');
            $saveData['number'] = $this->createGoodsNumber();
            $saveData['details'] = json_encode($saveData['details']);
            $this->save($saveData);

            if (!empty($saveData['id'])) {
                $this->delColor($saveData['id']);
            }

            foreach ($colors as $color) {
                $colorId = Db::table('product_color')->insertGetId([
                    'product_id'    => $this->id,
                    'color'         => $color['color'],
                    'img'           => $color['img']
                ]);
                foreach ($color['price'] as $price) {
                    Db::table('product_price')->insert([
                        'color_id'      => $colorId,
                        'configure'     => $price['configure'],
                        'trade_price'   => $price['trade_price']
                    ]);
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
        return $this->id;
    }

    /**
     * 软删除产品图片及其价格配置
     * @param $productId
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function delColor($productId)
    {
        $colorIds = Db::table('product_color')->where('product_id', $productId)->column('id');
        Db::table('product_color')->where('product_id', $productId)->update(['delete_time' => time()]);
        Db::table('product_price')->where('color_id', 'in', $colorIds)->update(['delete_time' => time()]);
    }

    /**
     * 生成产品编号
     * @return string
     */
    protected function createGoodsNumber()
    {
        $count = $this->where('factory_id', user_info('group_id'))->whereTime('create_time', 'today')->count();
        $id = user_info('group_id') >= 10 ? user_info('group_id') : '0' . user_info('group_id');
        return $id . date('ymd', time()) . sprintf("%02d",$count);
    }
}