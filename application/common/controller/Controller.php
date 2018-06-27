<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/2/21 
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\validate\BaseValidate;
use think\Controller as CoreController;
use think\Request;

abstract class Controller extends CoreController
{
    public $params = null;

    /**
     * 当前模型
     * @var
     */
    public $currentModel;

    /**
     * 当前 validate 类
     * @var
     */
    public $currentValidate;


    /**
     * 请求响应类
     * @var
     */
    public $response;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->response = new BaseValidate();
        if (!empty($this->request->param()))
            $this->params = $this->request->param();
    }



}