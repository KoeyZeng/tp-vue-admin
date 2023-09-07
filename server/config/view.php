<?php
// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板引擎类型使用Think
    'type'          => 'Think',
    // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
    'auto_rule'     => 1,
    // 缓存路径
    'cache_path'	=>	app()->getRuntimePath() . 'views/',
    // 缓存时间
    'cache_time'    => 0,
    // 静态页面路径
    'view_path'	    => app()->getRootPath() . 'view/web/',
    // 模板目录名
    'view_dir_name' => 'view',
    // 模板后缀
    'view_suffix'   => 'html',
    // 模板文件名分隔符
    'view_depr'     => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'     => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'       => '}',
    // 标签库标签开始标记
    'taglib_begin'  => '{',
    // 标签库标签结束标记
    'taglib_end'    => '}',
    // 模板变量
    'tpl_replace_string'  =>  [
        '__STATIC__'    => '/static',
        '__JS__'        => '/static/web/js',
        '__CSS__'       => '/static/web/css',
        '__IMG__'       => '/static/web/images',
        '__MJS__'        => '/static/mobile/js',
        '__MCSS__'       => '/static/mobile/css',
        '__MIMG__'       =>  '/static/mobile/images',
        '__URL__'       => env('app.url'),
        '__DEBUG__'     => env('app_debug') ? '1' : '0',
    ],
];
