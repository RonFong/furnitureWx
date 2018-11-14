<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/11/13 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\ShopCommodity as CoreShopCommodity;
use app\common\validate\BaseValidate;
use think\Db;

class ShopCommodity extends CoreShopCommodity
{

    /**
     * 发布|编辑商品
     * @param $param
     * @return array
     * @throws \app\lib\exception\BaseException
     */
    public function createCommodity($param)
    {
        try {
            Db::startTrans();
            $param['shop_id'] = user_info('group_id');
            if (!empty($param['id'])) {
                $param['create_time'] = time();
            }
            $this->save($param);
            foreach ($param['content'] as $k => $v) {
                if (!empty($v['text']) || !empty($v['img']) || !empty($v['video'])) {
                    $v['commodity_id'] = $this->id;
                    $v['sort'] = $k;
                    if (array_key_exists('style', $v) && !empty($v['style'])) {
                        $v['style'] = json_encode($v['style']);
                    }
                    $itemResult = (new ShopCommodityItem())->save($v);
                    if (!$itemResult) {
                        exception('内容块数据写入失败：'.json_encode($v));
                    }
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
        return ['id' => $this->id];
    }

    /**
     * 商品详情
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function commodityDetails($id)
    {
        $data = self::get(function ($query) use ($id) {
            $query->field('id, classify_name');
        });
        if ($data) {
            $contentId = $id;
            $data->content = ShopCommodityItem::all(function ($query) use ($contentId) {
                $query->where('commodity_id', $contentId)
                    ->order('sort');
            });
        }
        return $data;
    }

}