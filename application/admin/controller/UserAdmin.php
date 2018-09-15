<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\UserAdmin as CoreUserAdmin;
use think\Db;
use think\Request;

class UserAdmin extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreUserAdmin();//实例化当前模型
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
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $map = $this->getDataListMap();
        return $this->currentModel->where($map)->order('id desc')->layTable(['role_id_text', 'type_text']);
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        if (!empty($param['real_name'])) {
            $map['account'] = ['like', '%' . $param['account'] . '%'];//真实姓名
        }

        if (empty($map)) {
            $map[] = ['exp', '1=1'];
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
        $roleList = $this->currentModel->getRoleList();
        $this->assign('roleList', $roleList);

        /*随机头像*/
        $avatar = !empty($data['image']) ? $data['image'] : VIEW_STATIC_PATH . '/img/avatar/user' . rand(10, 50) . '.png';
        $this->assign('avatar', $avatar);

        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'UserAdmin');
        if ($result !== true) {
            $this->error($result);
        }

        try {
            //图片有变更，从临时文件夹移动图片，压缩头像100*100
            if (!empty($param['image'])) {
                $old = !empty($param['user_id']) ? Db::name('user')->where('user_id', $param['user_id'])->value('image') : '';
                if (empty($old) || $param['image'] != $old) {
                    $param['image'] = current(imgTempFileMove([$param['image']], 'admin/images/user/'));//从临时文件夹移动图片
                    if (strpos($param['image'], 'user/') && file_exists(PUBLIC_PATH . $param['image'])) {
                        \think\Image::open(PUBLIC_PATH . $param['image'])->thumb(100, 100)->save(PUBLIC_PATH . $param['image'], null, 100);
                    }
                    if (!empty($param['user_id'])) {
                        $count = Db::name('user')->where('image', $old)->count();//编辑且换图，则删除旧图片
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
}

