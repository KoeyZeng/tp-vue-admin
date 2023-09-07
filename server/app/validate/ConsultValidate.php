<?php
namespace app\validate;

use think\Validate;

// 咨询检验器
class ConsultValidate extends Validate
{
    protected $rule = [
        'name'          =>  'require|max:50',
        'sex'           =>  'require|between:0,2',
        'phone_code'    =>  'require',
        'phone'         =>  'require',
        'mail'          =>  'max:50',
        'price'         =>  'between:0,99999999999',
        'wechat'        =>  'require|max:50',
        'school'        =>  'max:50',
        'content'       =>  'require|max:250',
        'referer'       =>  'require|max:250',
    ];

    protected $message  =   [
        'name.require'          => '请输入姓名',
        'name.max'              => '姓名最多不能超过100个字符',
        'sex.require'           => '请选择性别',
        'phone_code.require'    => '请选择电话前缀',
        'phone.require'         => '请输入电话',
        'mail.max'              => '邮箱最多不能超过50个字符',
        'wechat.require'        => '请输入微信号',
        'wechat.max'            => '微信号最多不能超过50个字符',
        'price.between'         => '期望价格应该在0-99999999之间',
        'school.max'            => '学校最多不能超过50个字符',
        'content.require'       => '请输入咨询内容',
        'content.max'           => '咨询内容最多不能超过250个字符',
        'referer.require'       => '请输入访问来源',
        'referer.max'           => '访问来源最多不能超过250个字符',
    ];
}