<?php
namespace app\validate;

use think\Validate;

// 咨询检验器
class ActivityApplyValidate extends Validate
{
    protected $rule = [
        'name'          =>  'require|max:50',
        'sex'           =>  'require|between:0,2',
        'phone_code'    =>  'require',
        'phone'         =>  'require|max:11',
        'mail'          =>  'max:50',
        'wechat'        =>  'require|max:50',
        'school'        =>  'max:50',
        'address'       =>  'max:250',
        'book'          =>  'max:100',
        'screen'        =>  'max:100',
        'referer'       =>  'require|max:250',
    ];

    protected $message  =   [
        'name.require'          => '请输入姓名',
        'name.max'              => '姓名最多不能超过100个字符',
        'sex.require'           => '请选择性别',
        'sex.between'           => '性别应该在0-2之间',
        'phone_code.require'    => '请选择电话前缀',
        'phone.require'         => '请输入电话',
        'phone.max'             => '电话最多不能超过11个字符',
        'mail.max'              => '邮箱最多不能超过50个字符',
        'wechat.require'        => '请输入微信号',
        'wechat.max'            => '微信号最多不能超过50个字符',
        'school.max'            => '学校最多不能超过50个字符',
        'address.max'           => '地址最多不能超过250个字符',
        'book.max'              => '录取通知书最多不能超过100个字符',
        'screen.max'            => '屏幕截图最多不能超过100个字符',
        'referer.require'       => '请输入访问来源',
        'referer.max'           => '访问来源最多不能超过250个字符',
    ];
}