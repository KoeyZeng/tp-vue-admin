<?php

namespace app\common\exception;


use think\Exception;

class BaseException extends Exception
{
    public $code = 10000;   // 20000 - 成功 10000 - 失败
    public $msg = '处理异常!';
    public $data = [];
    public $statusCode; //状态码

    public function __construct($params = [], $statusCode=200){
        if( !is_array($params)){
           return ;
        }
        if(array_key_exists(0,$params)){
            $this->code = $params[0];
        }
        if(array_key_exists(1,$params)){
            $this->msg = $params[1];
        }
        if(array_key_exists(2,$params)){
            $this->data = $params[2];
        }
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('msg',$params)){
            $this->msg = $params['msg'];
        }
        if(array_key_exists('data',$params)){
            $this->data = $params['data'];
        }
        $this->statusCode = $statusCode;
    }

}