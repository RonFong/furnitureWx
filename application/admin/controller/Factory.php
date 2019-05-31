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
use app\common\model\GroupNearby;
use app\common\model\ProductColor;
use app\common\model\ProductPrice;
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
        return $this->currentModel->where($map)->order('id desc')->layTable(['admin_user_name', 'audit_state_text']);
    }

    private function getDataListMap()
    {
        $param = $this->request->param();
        $map = [];

        if (!empty($param['id'])) {
            $map['id'] = $param['id'];
        }
        if ($this->request->has('state', 'param', true)) {
            $map['state'] = $param['state'];
        }
        if ($this->request->has('audit_state', 'param', true)) {
            $map['audit_state'] = $param['audit_state'];
        }
        if (!empty($param['factory_name'])) {
            $map['factory_name'] = ['like', '%' . $param['factory_name'] . '%'];//厂家名
        }
        if (!empty($param['sales_phone'])) {
            $map['sales_phone'] = ['like', '%' . $param['sales_phone'] . '%'];// 门店联系人手机号
        }
        if (!empty($param['sales_contact'])) {
            $map['sales_contact'] = ['like', '%' . $param['sales_contact'] . '%'];//门店联系人
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
                ->join('factory_margin b', 'a.id = b.factory_id', 'LEFT')
                ->field('a.*,b.margin_fee')
                ->where('a.id', $param['id'])
                ->find();
            if (empty($data)) {
                $this->error('信息不存在');
            }
            $data = $data->toArray();
            $this->assign('data', $data);

            /*获取下拉列表：市*/
            $city = $this->getRegion(str_replace('省', '', $data['sales_province']));
            $this->assign('cityList', $city);

            /*获取下拉列表：区/镇*/
            $district = $this->getRegion(str_replace('市', '', $data['sales_city']));
            $this->assign('districtList', $district);
        }

        /*获取下拉列表：省份*/
        $provinceList = $this->getRegion(0);
        $this->assign('provinceList', $provinceList);

        /*获取下拉列表：管理员*/
        $user_list = Db::name('user')->field('id,user_name')->select();
        $this->assign('user_list', $user_list);

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
            //更新附近门店表
            $groupNearbyData = [
                'state'     => $param['state'],
                'audit_state'   => $param['audit_state'],
                'group_name'    => $param['factory_name']
            ];
            (new GroupNearby())->where(['group_type' => 1, 'group_id' => $this->currentModel->id])->update($groupNearbyData);
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
     * @param $val
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\exception\DbException
     */
    public function getRegion($val = 0)
    {
        if (is_numeric($val)) {
            $pid = !empty($val) ? $val : 0;
        }
        if (is_string($val)) {
            $pid = Db::name('district')->where('name', $val)->value('id');
        }
        return Db::name('district')->where('parent_id', $pid)->field('id,name,code')->select();
    }


    public function delete($id)
    {
        Db::startTrans();
        try {
            $this->currentModel->where('id', $id)->delete();
            (new \app\admin\model\User())->where(['type' => 1, 'group_id' => $id])->update(['type' => 3, 'group_id' => 0]);
            (new GroupNearby())->where(['group_id' => $id, 'group_type' => 1])->delete();
            $productModel = (new \app\admin\model\Product());
            $productIds = $productModel->where('factory_id', $id)->column('id');
            (new ProductColor())->where('product_id', 'in', implode(',', $productIds))->delete();
            (new ProductPrice())->where('product_id', 'in', implode(',', $productIds))->delete();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('删除成功');
    }


}

