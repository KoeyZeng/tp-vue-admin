<?php

namespace app;
use Closure;
use think\Config;
use think\Request;
use think\Response;
/**
 * 跨域请求支持
 */
class AllowCrossDomain
{
    protected $cookieDomain;
    protected $header = [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With, authKey, Accept, Origin, X-Token',
    ];
    public function __construct(Config $config)
    {
        $this->cookieDomain = $config->get('cookie.domain', '');
    }
    /**
     * 允许跨域请求
     * @access public
     * @param Request $request
     * @param Closure $next
     * @param array   $header
     * @return Response
     */
    public function handle($request, Closure $next, ?array $header = [])
    {
        $header = !empty($header) ? array_merge($this->header, $header) : $this->header;
        if (!isset($header['Access-Control-Allow-Origin'])) {
            $origin = $request->header('origin');
            if ($origin && ('' == $this->cookieDomain || strpos($origin, $this->cookieDomain))) {
                $header['Access-Control-Allow-Origin'] = $origin;
            } else {
                $header['Access-Control-Allow-Origin'] = '*';
            }
        }
        if ($request->method(true) == 'OPTIONS') {
            return Response::create()->code(204)->header($header);
        }
        return $next($request)->header($header);
    }
}