<?php
namespace app\validate;

use think\Validate;

// 资质管家检验器
class WorkValidate extends Validate
{
    protected $rule = [
        'name'          =>  'require|max:100',
        'phone'         =>  'mobile',
        'grade'         =>  'between:0,10',
        'code'          =>  'max:50',
        'province'      =>  'max:50',
        'city'          =>  'max:50',
        'county'        =>  'max:50',

    ];

    protected $message  =   [
        'name.require'         => '请输入证书名称',
        'name.max'             => '证书名称最多不能超过100个字符',
        'grade.between'        => '请选择证书级别',
        'province.max'         => '省份最多不能超过50个字符',
        'city.max'             => '市最多不能超过50个字符',
        'county.max'           => '区/县最多不能超过50个字符',
        'code.max'             => '证书编码最多不能超过50个字符',
    ];
}