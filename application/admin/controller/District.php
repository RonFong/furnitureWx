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

namespace app\admin\controller;


use think\Request;

class District extends BaseController
{
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = model('district');
    }

    public function getArea($id = 0)
    {
        return $this->currentModel->getArea($id);
    }
}