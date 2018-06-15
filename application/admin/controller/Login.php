<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2017/12/20 23:29
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\User;
use think\Controller;
use think\Session;

class Login extends Controller
{
    public function index()
    {
        if (session('user_info')) {
            $this->redirect('Index/index');
        } else {
            return $this->fetch();
        }
    }

    public function login()
    {
        $validate = validate('AdminUser');
        if(!$validate->check($this->request->param())){
            $this->error($validate->getError());
        }
        if(isset($user_info) && !empty($user_info)) {
            session('user_info',$user_info);
        }
        $backUrl = redirect()->restore()->getData();
        (new User())->setUserSession($this->request->param('account'));
        $this->success('success', $backUrl ? $backUrl : 'Index/index');
    }

    public function loginOut()
    {
        Session::clear();
        $this->redirect('Login/index');
    }
}