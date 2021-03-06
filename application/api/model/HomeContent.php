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
     * @return array
     */
    public function createData($param)
    {
        try {
            Db::startTrans();
            $param['group_id'] = user_info('group_id');
            $param['group_type'] = user_info('type');
            $this->save($param);
            foreach ($param['content'] as $k => $v) {
                if (!empty($v['text']) || !empty($v['img']) || !empty($v['video'])) {
                    $v['content_id'] = $this->id;
                    $v['sort'] = $k;
                    if (array_key_exists('style', $v) && !empty($v['style'])) {
                        $v['style'] = json_encode($v['style']);
                    }
                    $itemResult = (new HomeContentItem())->save($v);
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
        return ['id' => $this->id];
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
            //id 存在，但内容为空的，删除
            $updateIds = [];
            foreach ($param['content'] as $k => $v) {
                if (!empty($v['text']) || !empty($v['img']) || !empty($v['video'])) {
                    if (empty($v['id'])) {
                        unset($v['id']);
                    } else {
                        array_push($updateIds, $v['id']);
                    }
                    $v['content_id'] = $param['id'];
                    $v['sort'] = $k;
                    if (array_key_exists('style', $v) && !empty($v['style'])) {
                        $v['style'] = json_encode($v['style']);
                    }
                    (new HomeContentItem())->save($v);
                }
            }
            //删除
            $ids = array_diff($itemIds, $updateIds);
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
     * @param int $groupId
     * @param int $groupType
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function details($groupId = 0, $groupType = 0)
    {
        $groupId = $groupId ?: user_info('group_id');
        $groupType = $groupType ?: user_info('type');
        $data = self::get(function ($query) use($groupId, $groupType){
            $query->where(['group_id' => $groupId, 'group_type' => $groupType])
                ->field('id, music, music_name');
        });
        if ($data) {
            $contentId = $data['id'];
            $data->content = HomeContentItem::all(function ($query) use ($contentId) {
                $query->where('content_id', $contentId)
                    ->field(true)
                    ->field('video as video_snapshot, video as video_snapshot_auto')
                    ->order('sort');
            });
        }
        return $data;
    }

    /**
     * Emoji 表情符
     * @param $value
     * @return string
     */
    public function getTextAttr($value)
    {
        return $this->emojiDecode($value);
    }

    public function setTextAttr($value)
    {
        return $this->emojiEncode($value);
    }
}