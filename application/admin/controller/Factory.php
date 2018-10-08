<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Factory as CoreFactory;
use app\common\model\FactoryMargin;
use think\Db;
use think\Request;

class Factory extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreFactory();//实例化当前模型
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
        return $this->currentModel->where($map)->order('id desc')->layTable(['admin_user_name', 'audit_state_text']);
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        if (!empty($param['factory_name'])) {
            $map['factory_name'] = ['like', '%' . $param['factory_name'] . '%'];//厂家名
        }
        if (!empty($param['factory_phone'])) {
            $map['factory_phone'] = ['like', '%' . $param['factory_phone'] . '%'];//手机号
        }
        if (!empty($param['factory_contact'])) {
            $map['factory_contact'] = ['like', '%' . $param['factory_contact'] . '%'];//联系人
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
            $data = $this->currentModel
                ->alias('a')
                ->join('factoryMargin b', 'a.id = b.factory_id')
                ->where('a.id', $param['id'])
                ->find();
            if (empty($data)) {
                $this->error('信息不存在');
            }
            $data = $data->toArray();
            $this->assign('data', $data);

            /*获取下拉列表：市*/
            $city = $this->getRegion($data['province']);
            $this->assign('cityList', $city);

            /*获取下拉列表：区/镇*/
            $district = $this->getRegion($data['city']);
            $this->assign('districtList', $district);
        }

        /*获取下拉列表：省份*/
        $provinceList = $this->getRegion(0);
        $this->assign('provinceList', $provinceList);

        return $this->fetch();
    }

    public function save()
    {
        $param = $this->request->param();//获取请求数据

        if (empty($param)) {
            $this->error('没有需要保存的数据！');
        }

        //验证数据
        $result = $this->validate($param, 'Factory');
        if ($result !== true) {
            $this->error($result);
        }

        try {
            //保存数据
            $this->currentModel->save($param);
            if (!empty($param['margin'])) {
                if (!array_key_exists($param['margin'], config('system.margin_star'))) {
                    exception('保证金额度不合法');
                }
                $marginData = [
                    'factory_id'    => $param['id'],
                    'margin_fee'    => $param['margin']
                ];
                (new FactoryMargin())->save($marginData);
            }
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        $this->success('保存成功！', 'edit?id='.$this->currentModel->id);
    }

    /**
     * 根据pid 获取下拉列表，省市区三级联动
     * @param $pid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\exception\DbException
     */
    public function getRegion($pid)
    {
        $pid = !empty($pid) ? $pid : 0;
        return Db::name('district')->where('parent_id', $pid)->field('id,name,code')->select();
    }



}

