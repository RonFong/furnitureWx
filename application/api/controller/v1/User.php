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
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\User as userModel;
use app\api\service\Wechat;
use app\common\model\UserLocation;
use think\Request;
use app\api\validate\User as userValidate;

class User extends BaseController
{

    function __construct(Request $request = null)
    {

        parent::__construct($request);
        //当前model
        $this->currentModel = new userModel();
        //当前validate
        $this->currentValidate = new userValidate();
    }

    /**
     * 获取openid
     * @return mixed
     */
    public function getOpenid()
    {

        $weChat = new Wechat();
        $openid = $weChat->getOpenid(['code' => $_GET['code']]);
        $this->result['data'] = ['openid' => $openid];

        return json($this->result, 200);
    }


    /**
     * 用户修改头像
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function changeAvatar()
    {
        $this->currentValidate->goCheck('changeAvatar');

        try {
            $data['id'] = user_info('id');
            $data['avatar'] = $this->data['avatar'];
            $result = $this->currentModel->save($data);
            if (!$result) {
                exception('未知错误');
            }
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        $this->result['data'] = $this->data['avatar'];
        return json($this->result, 201);
    }


    /**
     * 用户修改昵称
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function changeName()
    {
        try {
            $strlen = mb_strlen(trim($this->data['userName']));
            if ($strlen < 1) {
                exception('请输入昵称');
            }
            if ($strlen > 7) {
                exception('昵称不能超过7个字');
            }
            $data['id'] = user_info('id');
            $data['user_name'] = $this->data['userName'];
            $result = $this->currentModel->save($data);
            if (!$result) {
                exception('未知错误');
            }
            $this->result['data'] = $this->data['userName'];
            return json($this->result, 201);
        } catch (\Exception $e) {
            $this->result['state'] = 0;
            $this->result['msg'] = $e->getMessage();
            return json($this->result, 999);
        }
    }


    /**
     * 用户名片
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function info()
    {
//        try {
            if (empty($this->data['user_id'])) {
                exception('user_id 不能为空');
            }
            $this->result['data'] = $this->currentModel->info($this->data['user_id']);
//        } catch (\Exception $e) {
//            $this->response->error($e);
//        }
        return json($this->result, 200);
    }


    /**
     * 保存用户位置
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function location()
    {
        $data = [
            'user_id'       => user_info('id'),
            'lng'           => sprintf("%.6f", $this->data['lng']),
            'lat'           => sprintf("%.6f", $this->data['lat']),
            'create_time'   => time()
        ];
        (new UserLocation())->save($data);
        return json($this->result, 200);
    }

}
