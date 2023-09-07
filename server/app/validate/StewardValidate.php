<?php
namespace app\validate;

use think\Validate;

// 资质管家检验器
class StewardValidate extends Validate
{
    protected $rule = [
        'name'          =>  'require|max:100',
        'phone'         =>  'mobile',
        'grade'         =>  'between:0,2',
    ];

    protected $message  =   [
        'name.require'         => '请输入姓名',
        'name.max'             => '姓名最多不能超过100个字符',
        'phone.mobile'         => '请输入正确的手机号码',
        'grade.between'        => '请选择等级',
    ];
}