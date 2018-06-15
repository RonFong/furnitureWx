<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2017/12/30 16:25
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\User as CoreUser;
use traits\model\SoftDelete;

class User extends CoreUser
{
    use SoftDelete;

    //头像（正方形）最大边长
    const IMAGE_MAX_LENGTH = 300;

    /**
     * 设置用户 session
     * @param $value
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function setUserSession($value)
    {
        $userInfo = self::get(['id|account' => $value]);
        unset($userInfo->password);
        if (!$userInfo) {
            return false;
        }
        session('user_info', $userInfo);

        $this->autoWriteTimestamp = false;  //不做更新时间记录

        //60s 时间间隔的登录操作被记录，并更新最后登录时间
        if ($userInfo->setInc('login_count', 1, 60) === 1) {
            $userInfo->login_last_time = time();
            $userInfo->save();
        }
        return true;
    }

    public function getGroupNameAttr($value, $data)
    {
        if ($data['factory_id']) {
            return Factory::where('id', $data['factory_id'])->value('factory_name');
        }
        if ($data['shop_id']) {
            return Shop::where('id', $data['shop_id'])->value('shop_name');
        }
        return '';
    }

    /**
     * 获取一个用户信息
     * @param array $map
     * @param bool $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getOne($map = [], $field = true)
    {
        return self::where($map)->field($field)->find();
    }

    /**
     * 获取可作为新建厂商初始用户的用户
     */
    public function getInitialUser()
    {
        //给新建的厂家分配初始用户(创建用户),此用户非后台管理员且未与任何厂商有关联
        $map['type'] = ['neq', 1];
        $map['factory_id'] = 0;
        $map['shop_id'] = 0;
        $map['status'] = 1;
        return self::all(function($query) use ($map) {
            $query->where($map)->field('id, user_name');
        });
    }


    /**
     * 保存方法
     * @param $saveData
     * @return mixed;
     */
    public function doSave($saveData)
    {
        $saveData['image'] = empty($saveData['org_image']) ? DEFAULT_IMAGE : $saveData['org_image'];
        if ($saveData['upload_image']) {
            $imgPath = self::uploadImg('image', 'user_icon');
            $saveData['image'] = $imgPath ?? $saveData['image'];
        }
        if (empty($saveData['id'])) {
            $saveData['salt_value'] = create_salt_value();
            $saveData['password'] = md5(md5($saveData['password']) . $saveData['salt_value']);
        }
        if(self::save($saveData)) {
            return $this->id;
        }
        return false;
    }


    public function factory()
    {
        return $this->hasOne('factory', 'factory_id', 'id')
            ->setEagerlyType(0)
            ->withField('user_id, factory_name')
            ->bind('user_id,factory_name');
    }

    public function shop()
    {
        return $this->hasOne('shop', 'shop_id', 'id')
            ->setEagerlyType(0)
            ->withField('user_id, shop_name')
            ->bind('user_id,shop_name');
    }
}