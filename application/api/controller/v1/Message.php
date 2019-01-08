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

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\common\model\WebsocketMessage;
use think\Request;
use app\api\validate\WebsocketMessage as WebsocketMessageValidate;

class Message extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->currentModel = new WebsocketMessage();
        $this->currentValidate = new WebsocketMessageValidate();
    }


    /**
     * 获取当前用户与某用户的聊天记录
     * @return \think\response\Json
     */
    public function logWithUser()
    {
        $this->currentValidate->goCheck('logWithUser');
        $param = $this->request->param();
        $this->result['data'] = $this->currentModel->logWithUser(user_info('id'), $param['toId'], $this->page, $this->row);
        return json($this->result, 200);
    }
}