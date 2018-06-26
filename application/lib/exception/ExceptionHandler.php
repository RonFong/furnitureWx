<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/15 
// +----------------------------------------------------------------------

namespace app\lib\exception;

use think\exception\Handle;
use think\Log;
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
                $this->msg = '内部错误';
                $this->errorCode = 999;
                $this->recordErrorLog($e);
            }
        }
        $request = Request::instance();
        $params = $request->param();
        if (!empty($params))
            unset($params['version']);
        $result = [
            'state' => 0,
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'method' => $request->method(),
            'request_url' => $request->url(),
            'params' => $params
        ];
        return json($result, $this->code);
    }

    private function recordErrorLog(\Exception $e)
    {
        Log::init(
            [
                'type' => 'File',
                'path' => ERROR_LOG_PATH,
                'level' => ['error']
            ]);
        Log::record($e->getMessage(), 'error');
    }
}