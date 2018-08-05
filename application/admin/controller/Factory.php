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
use think\Request;

class Factory extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new CoreFactory();//实例化当前模型
    }



}

