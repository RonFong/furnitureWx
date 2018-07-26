<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/15 
// +----------------------------------------------------------------------

namespace app\lib\exception;

use think\Db;
use think\exception\Handle;
use think\Request;

/**
 * 异常类
 * Class ExceptionHandler
 * @package app\lib\exception
 */
class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    private $systemMsg = '请稍后再试~';

    /**
     * 重写框架Handle父类方法
     * @param \Exception $e
     * @return \think\Response|\think\response\Json
     */
    public function render(\Exception $e)
    {
        if ($e instanceof BaseException) {
            //如果是自定义的异常
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            //根据调试模式判断是否抛出错误
            if (config('app_debug')) {
                return parent::render($e);
            } else {
                $this->code = 500;
                $this->msg = $this->systemMsg;
                $this->errorCode = 999;
            }
        }
        $request = Request::instance();
        $params = $request->param();
        if (!empty($params))
            unset($params['version']);
        $result = [
            'state'         => 0,
            'msg'           => $this->msg,
            'data'          => [],
            'error_code'    => $this->errorCode,
            'method'        => $request->method(),
            'request_url'   => $request->url(),
            'params'        => $params,
        ];
        if ($this->code == 500) {
            $this->recordErrorLog($result);
            if (!config('app_debug')) {
                $result['msg'] = $this->systemMsg;
            }
        }
        return json($result, $this->code);
    }

    /**
     * 写入错误日志表
     * @param $data
     */
    private function recordErrorLog($data)
    {
        $logData = [
            'url'       => $data['request_url'],
            'time'      => date('Y-m-d H:i:s', time()),
            'ip'        => Request::instance()->ip(1),
            'params'    => json_encode($data['params']),
            'user_id'   => user_info('id'),
            'msg'       => $data['msg'],
        ];
        Db::table('error_log')->insert($logData);
    }
}