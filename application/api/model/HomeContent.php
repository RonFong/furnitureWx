<?php
namespace app\api\model;

use app\common\model\HomeContent as CoreHomeContent;
use app\common\validate\BaseValidate;
use think\Db;

class HomeContent extends CoreHomeContent
{

    /**
     * 创建图文
     * @param $param
     * @return bool
     * @throws \app\lib\exception\BaseException
     */
    public function createData($param)
    {
        try {
            Db::startTrans();
            $param['group_id'] = user_info('group_id');
            $param['group_type'] = user_info('type');
            $this->save($param);
            foreach ($param['content'] as $k => $v) {
                $v['content_id'] = $this->id;
                $v['sort'] = $k;
                if (array_key_exists('style', $v) && !empty($v['style'])) {
                    $v['style'] = json_encode($v['style']);
                }
                $itemResult = (new HomeContentItem())->save($v);
                if (!$itemResult) {
                    exception('内容块数据写入失败：'.json_encode($v));
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
     * 更新
     * @param $param
     * @return bool
     * @throws \app\lib\exception\BaseException
     */
    public function updateData($param)
    {
        try {
            Db::startTrans();
            $this->save($param);
            $itemModel = new HomeContentItem();
            $itemIds = $itemModel->where('content_id', $param['id'])->column('id');
            foreach ($param['content'] as $k => $v) {
                if (empty($v['id'])) {
                    unset($v['id']);
                }
                $v['content_id'] = $param['id'];
                $v['sort'] = $k;
                if (array_key_exists('style', $v) && !empty($v['style'])) {
                    $v['style'] = json_encode($v['style']);
                }
                (new HomeContentItem())->save($v);
            }
            //删除
            $ids = array_diff($itemIds, array_column($param['content'], 'id'));
            if ($ids) {
                $itemModel->where('id', 'in', implode(',', $ids))->delete();
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
        return true;
    }

    /**
     * 获取详情
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function details()
    {
        $data = self::get(function ($query) {
            $query->where(['group_id' => user_info('group_id'), 'group_type' => user_info('type')])
                ->field('id, music, music_name');
        });
        if ($data) {
            $contentId = $data['id'];
            $data->content = HomeContentItem::all(function ($query) use ($contentId) {
                $query->where('content_id', $contentId)
                    ->field('content_id', true)
                    ->order('sort');
            });
        }
        return $data;
    }
}