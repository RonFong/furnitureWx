<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/15 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\User as CoreUser;


class User extends CoreUser
{
    /**
     * 注册 |  更新
     * @param $data
     * @return false|int
     * @throws \think\exception\DbException
     */
    public function saveData($data)
    {
        $this->data($data);
        $this->save();
        return $this;
    }

    /**
     * 用户删除
     * @param $id
     * @return int
     */
    public function deleteUser($id)
    {
        $map['id'] = is_array($id) ? ['in', $id] : $id;
        //TODO 事务、头像删除、信息判断
        $result = $this->where($map)->delete();
        return $result;
    }

    /**
     * 查找用户
     * @param $map
     * @param $page
     * @param $row
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\exception\DbException
     */
    public function selectUser($map, $page, $row)
    {
        return $this->where($map)->page($page, $row)->select();
    }

}