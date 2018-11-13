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
     * 发布商品
     * @param $param
     * @return array
     * @throws \app\lib\exception\BaseException
     */
    public function createCommodity($param)
    {
        try {
            Db::startTrans();
            $param['shop_id'] = user_info('group_id');
            $classifyData = [
                'group_id'      => user_info('group_id'),
                'group_type'    => user_info('type'),
                'classify_name' => trim($param['classify_name'])
            ];
            $classifyId = Db::table('group_classify')->where($classifyData)->value('id');
            if (!$classifyId) {
                $classifyData['create_time'] = time();
                $classifyId = Db::table('group_classify')->insertGetId($classifyData);
            }
            $param['classify_id'] = $classifyId;

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

}