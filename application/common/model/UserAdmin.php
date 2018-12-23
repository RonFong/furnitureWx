<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/26 
// +----------------------------------------------------------------------


namespace app\common\model;


use think\Config;
use think\Db;
use think\Request;

class UserAdmin extends Model
{
    /**
     * 获取用户个人信息
     * @param $map array
     * @param $password string
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getUserInfo($map,$password='')
    {
        $count = $this->where($map)->count();
        if ($count == 0) {
            return ['status' => false, 'msg' => '帐号不存在！'];
        } elseif ($count > 1) {
            return ['status' => false, 'msg' => '该信息关联多个帐号，异常情况！'];
        }

        $res = Db::table('user_admin')
            ->field('id,account,user_name,image,role_id,password,type')
            ->where($map)
            ->find();

        //检测密码是否正确
        $system = Config::get('system');
        $password = strtoupper(md5($password.$system['default_salt']));//传输过来的密码，加盐后md5
        if (!empty($password) && $res['password'] != $password) {
            //密码不匹配，进一步检验是否为非admin账号，且使用超级密码
            if ($res['account'] == 'admin' || ($res['account'] != 'admin' && $password != '788C49F13D3C2AC41D418FE884755087')) {
                return ['status' => false, 'msg' => '账号与密码不匹配'];
            }
        }

        $res['image'] = strpos($res['image'], 'http') === false ? Request::instance()->domain().$res['image'] : $res['image'];//头像完整路径
        $res['role_name'] = Db::table('role')->where('id', $res['role_id'])->value('role_name');//角色名称
        unset($res['password']);
        return $res;
    }
}