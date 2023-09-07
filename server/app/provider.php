<?php
// use app\ExceptionHandle;
use app\Request;
use app\common\exception\ExceptionHandle;
// 容器Provider定义文件
return [
    'think\Paginator'        => 'app\web\paginator\Bootstrap',
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
