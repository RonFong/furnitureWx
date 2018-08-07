<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\UserAdmin;
use think\Controller;
use app\admin\model\User;
use think\Cache;
use think\Cookie;
use think\Db;
use think\Session;

class Login extends Controller
{
    /**
     * 登录页面
     * @return mixed
     */
    public function index()
    {
        //若session已存在，则直接跳往后台主页
        if (!empty(user_info('id'))) {
            $this->redirect('Index/index');
        }

        return $this->fetch();
    }

    /**
     * 登录处理
     */
    public function login()
    {
        $param = $this->request->param();//获取参数
        if (empty($param['keyword']) || empty($param['password'])) {
            $this->error('用户名 或 密码不能为空！');
        }

        //验证码校验
//        if( cookie('error_num') > 2 && (empty($param['vercode']) || !captcha_check($param['vercode']))){
//            $this->error('验证码错误');
//        }

        //记住密码
        if(isset($param['remember']) && $param['remember'] == 1){
            Cookie::set('remember', 1);
            Cookie::set('remember_name', $param['keyword']);
            Cookie::set('remember_pwd', $param['password']);
        } else {
            Cookie::set('remember', null);
            Cookie::set('remember_name', null);
            Cookie::set('remember_pwd', null);
        }

        //记住登陆前的页面，登录成功后跳转
        Cookie::set('back_url', redirect()->restore()->getData());

        $data['account'] = $param['keyword'];
        $data['password'] = $param['password'];
        $this->loginGo($data);//设置session，实现登录并跳转
    }

    /**
     * 设置session，实现登录并跳转
     * @param $data
     */
    public function loginGo($data)
    {
        $password = '';
        if (!empty($data['password'])) {
            $password = $data['password'];
            unset($data['password']);
        }

        $url = !empty(Cookie::get('back_url')) ? Cookie::get('back_url') : 'Index/index';
        try{
            /*获取用户数据*/
            $User = new UserAdmin();
            $userInfo = $User->getUserInfo($data, $password);

            //返回错误信息
            if(isset($userInfo['status']) && $userInfo['status'] == false){
                throw new \Exception($userInfo['msg']);
            }
            $this->setSession($userInfo);//设置session
        } catch (\Exception $e) {
            cookie('error_num', cookie('error_num') + 1);
            $this->error($e->getMessage());
        }

        Cookie::set('back_url', null);
        if ($this->request->isAjax()) {
            $this->success('登录成功！', $url);
        } else {
            $this->redirect($url);
        }
    }


    /**
     * 忘记密码，找回密码页面
     * @return mixed
     */
    public function forget()
    {
        return $this->fetch();
    }

    /**
     * 重置密码页面
     * @return mixed
     */
    public function passwordReset()
    {
        return $this->fetch();
    }

    /**
     * 保存密码
     */
    public function passwordSave()
    {
        $this->success('操作成功！');
    }

    /**
     * 退出登录
     */
    public function loginOut()
    {
        Session::clear();
        $this->redirect('index');
    }

    /**
     * 保存用户信息到session
     * @param $data
     */
    public function setSession($data)
    {
        //用户信息
        if (is_object($data)) {
            $data = collection($data)->toArray();
        }
        Session::set('user_info', $data);

        cookie('error_num', 0);//错误次数归0
    }

    /**
     * 清除缓存
     * @param int $jump_wait
     */
    public function clearCache($jump_wait = 1)
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        Cache::clear();
        array_map('unlink', glob(TEMP_PATH . DS . '*.php'));
        if ($jump_wait == 1) {
            $this->success('清除缓存成功');
        } else {
            $this->redirect('Index/index');
        }
    }
}