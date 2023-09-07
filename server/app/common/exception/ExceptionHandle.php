<?php
namespace app\common\exception;

use think\exception\Handle;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{

    private $code;
    private $msg;
    private $data;
    private $statusCode;

    public function render($request, Throwable $e): Response
    {
        if ($e instanceof BaseException){
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->data = $e->data;
        }else {
            $this->code = 50014;
            $this->msg = env('APP_DEBUG') != 1 ? 'system error' : $e->getLine().'#: '.$e->getMessage();
            $this->data = '';
            // 调试状态时使用原来的错误页面显示
            if (env('APP_DEBUG') == 1) return parent::render($request,  $e);
        }
        // 状态码
        $this->statusCode = $e->statusCode ?? 200;

        return json([
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data,
        ], $this->statusCode);
    }
}
