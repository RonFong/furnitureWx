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

namespace app\api\model;

use app\common\model\FactoryIntro as CoreFactoryIntro;
use app\common\validate\BaseValidate;
use think\Db;


/**
 * 厂家简介
 * Class FactoryIntro
 * @package app\api\model
 */
class FactoryIntro extends CoreFactoryIntro
{
    /**
     * @param $param
     * @return bool
     */
    public function createData($param)
    {
        try {
            Db::startTrans();
            $param['factory_id'] = user_info('group_id');
            $this->save($param);
            foreach ($param['content'] as $k => $v) {
                if (!empty($v['text']) || !empty($v['img']) || !empty($v['video'])) {
                    $v['factory_id'] = $param['factory_id'];
                    $v['sort'] = $k;
                    if (array_key_exists('style', $v) && !empty($v['style'])) {
                        $v['style'] = json_encode($v['style']);
                    }
                    $itemResult = (new FactoryIntroContent())->save($v);
                    if (empty($v['id']) && !$itemResult) {
                        exception('内容块数据写入失败：'.json_encode($v));
                    }
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
        return true;
    }


    /**
     * 修改
     * @param $param
     * @return bool
     * @throws \app\lib\exception\BaseException
     */
    public function updateData($param)
    {
        try {
            Db::startTrans();
            $this->save($param);
            $itemModel = new FactoryIntroContent();
            $itemIds = $itemModel->where('factory_id', $param['factory_id'])->column('id');
            //id 存在，但内容为空的，删除
            $updateIds = [];
            foreach ($param['content'] as $k => $v) {
                if (empty($v['text']) && empty($v['img']) && empty($v['video'])) {
                    continue;
                }
                if (empty($v['id'])) {
                    unset($v['id']);
                } else {
                    array_push($updateIds, $v['id']);
                }
                $v['sort'] = $k;
                if (array_key_exists('style', $v) && !empty($v['style'])) {
                    $v['style'] = json_encode($v['style']);
                }
                $v['factory_id'] = $param['factory_id'];
                (new FactoryIntroContent())->save($v);
            }
            //删除
            $ids = array_diff($itemIds, $updateIds);
            if ($ids) {
                $itemModel->where('id', 'in', implode(',', $ids))->update(['delete_time' => time()]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
        return true;
    }

    /**
     * 获取简介详情
     * @param $factoryId
     * @return array|false|static[]
     */
    public function introInfo($factoryId)
    {
        $isExist = self::get($factoryId);
        if (!$isExist) {
            return [];
        }
        return FactoryIntroContent::all(function ($query) use ($factoryId) {
            $query->where('factory_id', $factoryId)
                ->field(true)
                ->field('video as video_snapshot, video as video_snapshot_auto')
                ->field('delete_time', true)
                ->order('sort');
        });
    }
}