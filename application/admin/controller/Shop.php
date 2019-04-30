<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Shop as CoreShop;
use app\common\model\StoreClassify;
use app\lib\oss\Oss;
use think\Db;
use think\Request;

class Shop extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreShop();//实例化当前模型
    }

    /**
     * 列表页
     * @return mixed
     */
    public function index()
    {
        $this->assign('id', $this->request->param('id') ?? 0);
        return $this->fetch();
    }

    /**
     * 列表页，获取数据
     * @return mixed
     */
    public function getDataList()
    {
        $map = $this->getDataListMap();

        $list = $this->currentModel
            ->join('__USER__', 'shop.admin_user=user.id')
            ->where($map)
            ->order('shop.id desc')
            ->field('shop.id,shop.admin_user,shop.shop_name,user.user_name,user.create_time,user.create_time,
            shop.shop_contact,shop.shop_phone,shop.shop_wx,shop.address,shop.audit_state,shop.state,shop.lat,shop.lng')
            ->layTable(['audit_state_des', 'home_content_has', 'shop_commodity_count', 'last_login_time', 'all_login_times', 'all_login_times_month']);
        return $list;
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        $map = [];
        if (!empty($param['id'])) {
            $map['shop.id'] = $param['id'];
        }
        if (!empty($param['shop_name'])) {
            $map['shop_name'] = ['like', '%' . $param['shop_name'] . '%'];//商户名
        }
        if (!empty($param['shop_phone'])) {
            $map['shop_phone'] = ['like', '%' . $param['shop_phone'] . '%'];//手机号
        }
        if (!empty($param['shop_contact'])) {
            $map['shop_contact'] = ['like', '%' . $param['shop_contact'] . '%'];//联系人
        }
        if ($param['state'] !== '') {
            $map['state'] = $param['state'];
        }
        if ($param['audit_state'] !== '') {
            $map['audit_state'] = $param['audit_state'];
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

        //经营类别
        $categoryList = StoreClassify::all(function ($query) {
            $query->where(['state' => 1, 'parent_id' => 0])->field('id, name');
        });

        $this->assign('categoryList', $categoryList);

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
            //保存数据
            $this->currentModel->save($param);
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

    /**
     * 显示门店位置（地图）
     * @param string $lat
     * @param string $lng
     * @return mixed
     */
    public function showMap($lat, $lng)
    {
        $data = [];
        $data['lat'] = $lat;
        $data['lng'] = $lng;
        $this->assign('data', json_encode($data));
        return $this->fetch('show_map');
    }
}

