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
    private $location = '';
    private $systemMsg = '请稍后再试~';

    /**
     * 重写框架Handle父类方法
     * @param \Exception $e
     * @return \think\Response|\think\response\Json
     */
    public function render(\Exception $e)
    {
        if ($e instanceof BaseException) {
            //被捕获的异常, msg == '' 时， 为嵌套的 try catch 所捕获的异常
            $errInfo = $e->msg == '' ? $e->getTrace()[0]['args'][0] : $e;
            $this->code = $errInfo->code;
            $this->msg = $errInfo->msg;
            $this->errorCode = $errInfo->errorCode;
            if ($e->msg !== '') {
                $this->location = 'line:' . $e->getTrace()[0]['line'] . ' ' . $e->getTrace()[0]['file'];
            }
        } else {
            //违背捕获的异常 根据调试模式判断是否抛出错误
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
            'error_location'=> $this->location,
            'method'        => $request->method(),
            'request_url'   => $request->url(),
            'params'        => $params
        ];
        if ($this->code == 500) {
            $result['msg'] = $e->msg;
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
            'params'    => is_array($data['params']) ? json_encode($data['params']) : $data['params'],
            'user_id'   => user_info('id') ?? 0,
            'msg'       => $data['msg'],
            'error_location' => $this->location
        ];
        Db::table('error_log')->insert($logData);
    }
}