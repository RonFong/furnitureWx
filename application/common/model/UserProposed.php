<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/21 
// +----------------------------------------------------------------------


namespace app\common\model;
use think\Db;

/**
 * 推荐关系
 * Class UserProposed
 * @package app\common\model
 */
class UserProposed extends Model
{
    public function saveData($param)
    {
        try {
            if ($this->where(['proposed_id' => user_info('id')])->find()) {
                return true;
            }
            $data = [
                'user_id'       => $param['referrer_id'],
                'proposed_id'   => user_info('id'),
                'create_time'   => time()
            ];
            $this->save($data);
            $this->collect($param);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * 收藏门店
     * @param $param
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collect($param)
    {
        //如果推荐者为 门店用户，则为被推荐者收藏
        $userInfo = User::get($param['referrer_id']);
        $collectData = [
            'user_id'       => user_info('id')
        ];
        switch ($userInfo['type']) {
            case 1: //推荐者为厂家
                $collectData['factory_id'] = $userInfo['group_id'];
                if (!Db::table('relation_factory_collect')->where($collectData)->find()) {
                    $collectData['create_date'] = date('Ymd', time());
                    $collectData['create_time'] = time();
                    Db::table('relation_factory_collect')->insert($collectData);
                }
                break;
            case 2:  //推荐者为商家
                $collectData['shop_id'] = $userInfo['group_id'];
                if (!Db::table('relation_shop_collect')->where($collectData)->find()) {
                    $collectData['create_date'] = date('Ymd', time());
                    $collectData['create_time'] = time();
                    Db::table('relation_shop_collect')->insert($collectData);
                }
                break;
            default:
                return true;
        }
        return true;

    }
}