<?php
namespace app\validate;

use think\Validate;

// 后台用户检验器
class AdminUserValidate extends Validate
{
    protected $rule = [
        'username'  =>  'require|max:30',
        'password' =>   'require|max:50',
    ];
    
    protected $message  =   [
        'username.require'      => '请输入登录账号',
        'username.max'          => '登录账号最多不能超过30个字符',
        'password.require'      => '请输入登录密码',
        'password.max'          => '登录密码最多不能超过50个字符',
    ];
}