<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/13 
// +----------------------------------------------------------------------
namespace app\api\controller;

use \app\common\controller\Controller;
use think\Hook;
use think\Request;

abstract class BaseController extends Controller {

    /**
     * 调用数据
     * @var array
     */
    protected $data;

    /**
     * 请求属性
     * @var \think\Request
     */
    protected $request;

    /**
     * state 0为失败|1为成功
     * msg 提示语
     * data 数据
     * @var array
     */
    protected $result = [
        'state' => 1,
        'msg'   => 'success',
        'data'  => [],
    ];

    /**
     * 文件
     * @var
     */
    protected $files;

    protected $page;

    protected $row;

    public function __construct(Request $request = null)
    {
        Hook::listen('app_init');
        Hook::listen('action_init');

        parent::__construct($request);
        $params = $request->param();
        unset($params['version']);
        $this->data = $params;
        $this->files = $request->file();
        $this->page = isset($this->data['page']) ? $this->data['page'] : 1;
        $this->row  = isset($this->data['row']) ? $this->data['row'] : 10;
    }


}