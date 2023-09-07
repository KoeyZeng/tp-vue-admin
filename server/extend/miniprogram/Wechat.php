<?php

namespace miniprogram;

use EasyWeChat\Factory;
use think\facade\Cache;

/**
 * 微信小程序认证类
 *
 * @author ZWJ
 */
class Wechat
{
    protected  $appID ;
    protected  $appSecret;
    protected  $payMchId;
    protected  $payKey;
    protected  $notify_url;
    protected  $app;


    public function __construct(){
        // 小程序APPID
        $this->appID = config('wechat.mini_app_id');
        // 小程序APP秘钥O
        $this->appSecret = config('wechat.mini_app_secret');
        // 商户关联ID
        $this->payMchId = config('wechat.pay_mch_id');
        // API 密钥
        $this->payKey = config('wechat.pay_key');
        // 回调地址
        $this->notify_url = config('notify_url');

    }

    // 获取用户openid信息
    public function getUserInfo($code){
        $config = [
            'app_id' => $this->appID,
            'secret' => $this->appSecret,
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            // 微信错误日志
            'file' => app()->getRootPath().'/runtime/wechat/program.log',
            'log' => [
                'level' => 'error', // debug/info/notice/warning/error/critical/alert/emergency
            ],
        ];
        return Factory::miniProgram($config)->auth->session($code);
    }

    // 获取支付信息
    public function pay(){
        $config = [
            // 必要配置
            'app_id'             => $this->appID,
            'mch_id'             => $this->payMchId,
            'key'                => $this->payKey,   // API 密钥

            'notify_url'         => $this->notify_url,     // 你也可以在下单时单独设置来想覆盖它
        ];
        $this->app = Factory::payment($config);
    }

    // 微信支付
    public function payWechat($unify = []){
        $this->pay();
        $config = [
            'body' => '腾讯充值中心-QQ会员充值',
            'out_trade_no' => '20150806125346',
            'total_fee' => 88,
            'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
        ];
    }
}