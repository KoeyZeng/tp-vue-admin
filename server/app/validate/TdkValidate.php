<?php
namespace app\validate;

use think\Validate;

// 资质管家检验器
class TdkValidate extends Validate
{
    protected $rule = [
        'title'     => 'require|length:2,255',
        'keywords'     => 'require|length:2,255',
        'description'     => 'require|length:2,255',
    ];

    protected $message  =   [
        'title.require'  => '请输入title',
        'title.length'   => 'title需在2~255之间',
        'keywords.require'  => '请输入keywords',
        'keywords.length'   => 'keywords需在2~255之间',
        'description.require'  => '请输入description',
        'description.length'   => 'description需在2~255之间',
    ];
}