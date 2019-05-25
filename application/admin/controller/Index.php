<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Db;
use think\Request;

class Index extends Base
{
    public function index()
    {
        $map = [];
        //文章数
        $article = [];
        $article['count'] = Db::name('article')->where($map)->count();
        $article['count_month'] = Db::name('article')->where('create_time', '>=', strtotime(date('Y-m-01')))->where($map)->count();
        $this->assign('article', $article);

        //厂家数
        $factory = [];
        $factory['count'] = Db::name('factory')->where($map)->count();
        $factory['count_month'] = Db::name('factory')->where('create_time', '>=', strtotime(date('Y-m-01')))->where($map)->count();
        $this->assign('factory', $factory);

        //商家数
        $shop = [];
        $shop['count'] = Db::name('shop')->count();
        $shop['count_month'] = Db::name('shop')->where('create_time', '>=', strtotime(date('Y-m-01')))->count();
        $this->assign('shop', $shop);

        //产品数
        $goods = [];
        $goods['count'] = Db::name('product')->where($map)->count();
        $goods['count_month'] = Db::name('product')->where('create_time', '>=', strtotime(date('Y-m-01')))->where($map)->count();
        $this->assign('goods', $goods);

        //待审核的商家
        $reviewData['shop'] = Db::table('shop')->where(['state' => 1, 'audit_state' => 0])->count();
        //待审核的厂家
        $reviewData['factory'] = Db::table('factory')->where(['state' => 1, 'audit_state' => 0])->count();
        //待审核的产品
        $reviewData['product'] = Db::table('product')->where(['state' => 1, 'review_status' => 0])->count();
        $this->assign('reviewData', $reviewData);

        return $this->fetch();
    }


}

