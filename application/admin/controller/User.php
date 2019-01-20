<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\User as CoreUser;
use Carbon\Carbon;
use think\Db;
use think\Request;
use think\Session;

class User extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreUser();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }


    /**
     * 列表页
     * @return mixed
     */
    public function time()
    {
        echo Carbon::now()->diffForHumans(Carbon::now()->subYear());
        die('1');
    }

    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $map = $this->getDataListMap();
        $list = $this->currentModel->where($map)->order('id desc')
            ->field('id,user_name,type,gender,state,phone,group_id,create_time')
            ->layTable(['last_login_time', 'all_login_times', 'all_login_times_month']);
        return $list;
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        $map = [];
        if (!empty($param['user_name'])) {
            $map['user_name'] = ['like', '%' . $param['user_name'] . '%'];//帐号
        }
        if (!empty($param['phone'])) {
            $map['phone'] = ['like', '%' . $param['phone'] . '%'];//帐号
        }
        if (!empty($param['type'])) {
            $map['type'] = ['like', '%' . $param['type'] . '%'];//类型
        }
        return $map;
    }

    /**
     * 编辑
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $param = $this->request->param();

        if (!empty($param['id'])) {
            $data = $this->currentModel->where('id', $param['id'])->find();
            if (empty($data)) {
                $this->error('信息不存在');
            }
            $data = $data->toArray();
            $this->assign('data', $data);
        }

        /*随机头像*/
        $avatar = !empty($data['avatar']) ? $data['avatar'] : VIEW_STATIC_PATH . '/img/avatar/user' . rand(10, 50) . '.png';
        $this->assign('avatar', $avatar);

        //厂家列表
        $type = !empty($data['type']) ? $data['type'] : 0;
        $groupList = $this->currentModel->getGroupList($type);
        $this->assign('groupList', $groupList);

        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'User');
        if ($result !== true) {
            $this->error($result);
        }

        try {
            //图片有变更，从临时文件夹移动图片，压缩头像100*100
            if (!empty($param['avatar'])) {
                $old = !empty($param['user_id']) ? Db::name('user')->where('user_id', $param['user_id'])->value('avatar') : '';
                if (empty($old) || $param['avatar'] != $old) {
                    $param['avatar'] = current(imgTempFileMove([$param['avatar']], 'admin_fussen/images/user/'));//从临时文件夹移动图片
                    if (strpos($param['avatar'], 'user/') && file_exists(PUBLIC_PATH . $param['avatar'])) {
                        \think\Image::open(PUBLIC_PATH . $param['avatar'])->thumb(100, 100)->save(PUBLIC_PATH . $param['avatar'], null, 100);
                    }
                    if (!empty($param['user_id'])) {
                        $count = Db::name('user')->where('avatar', $old)->count();//编辑且换图，则删除旧图片
                        //若其他地方没使用该旧图片，则删除
                        if ($count == 1){
                            delete_file($old);
                        }
                    }
                }
            }
            //保存数据
            $this->currentModel->save($param);
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        $this->success('保存成功！', 'edit?id='.$this->currentModel->id);
    }


    /**
     * 修改密码
     */
    public function changePassword()
    {
        $param = $this->request->param();

        if (empty($param['password'])) {
            $this->error('新密码不能为空');
        }

        if ($param['password'] != $param['confirm']) {
            $this->error('两次输入的密码不一致');
        }

        //保存数据
        $res = $this->currentModel->save($param);
        if ($res === false) {
            $this->error($this->currentModel->getError());
        }

        $this->success('保存成功!');
    }

}

