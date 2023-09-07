<?php
// 应用公共文件

if (!function_exists('get_client_ip')) {
    /**
     * 获取客户端IP
     * @return string  响应结果
     */
    function get_client_ip(){
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "";
        return $ip;
    }
}

if (!function_exists('exception')) {
    /**
     * 抛出异常处理
     * @param string    $msg  异常消息
     * @param integer   $code 异常代码 默认为0
     * @param string    $exception 异常类
     * @throws Exception
     */
    function exception($msg = '', $code = 10000, $exception = '')
    {
        $e = $exception ?: '\think\Exception';
        throw new $e($msg, $code);
    }
}

if (!function_exists('password')) {

    /**
     * 密码加密算法
     * @param string $value 需要加密的值
     * @param string $type  加密类型，默认为md5 （md5, hash）
     * @return mixed
     */
    function password($value)
    {
        $value = $value.env('jwt.key');
        $value = sha1('blog_') . md5($value) . md5('_encrypt') . sha1($value);
        return sha1($value);
    }

}
if (!function_exists('is_https')) {
    /**
     * 检验协议
     * @return mixed
     */
    function is_https(){
        if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'){
            return "https://";
        }
        else if (! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'){
            return "https://";
        }
        else if (! empty($_SERVER['HTTP_X_CLIENT_PROTO']) && strtolower($_SERVER['HTTP_X_CLIENT_PROTO']) === 'https'){
            return "https://";
        }
        else if (! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off'){
            return "https://";
        }
        return "http://";
    }
}

if(!function_exists('set_config')) {
    /**
     * 修改配置文件，目前仅支持（配置值为字符串）
     * @param  string $name 配置名称
     * @param  array  $value 需要更新或添加的配置
     * @return bool
     */
    function set_config($name = '', $value = '') {
        if(is_string($name)){
            if (false === strpos($name, '.')) {
                $name = 'ep.' . $name;
            }
            $name = explode('.', $name, 2);

            // 转义单引号
            $value = str_replace('\'','\\\'', $value);

            $file = app()->getRootPath().'config/'.$name[0].".php";

            $string = file_get_contents($file); //加载配置文件

            $pattern = '/\'' . $name[1] . '\'(.*?)=>(.*?)\'(.*?)\',/';
            $replacement = "'{$name[1]}'$1=>$2'{$value}',";
            $string = preg_replace($pattern, $replacement, $string); // 正则查找然后替换
            file_put_contents($file, $string, LOCK_EX); // 写入配置文件
            return true;
        }else {
            return false;
        }
    }
}

if(!function_exists('encode_jwt')) {

    /**
     * jwt加密
     * @access protected
     * @param int $uid 用户ID
     * @return array
     */
     function encode_jwt($uid,$token = ''){
        if (strlen($uid) < 1) exception(0,'无法获取用户ID');
        $key = env('jwt.key', '');          // 加密密钥
        $time = time();                     // 当前时间
        $exp = env('jwt.exp',3600 * 24 * 7); // 7天有效
        $hash = env('jwt.hash','HS256');
        $uip = get_client_ip();
        $data = [
            'iat' => $time, //签发时间
            'nbf' => $time, //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time + $exp, //过期时间
            'uid' => $uid,  //用户ID
            'uip' => $uip,  //用户IP
        ];
        if(strlen($token)) $data['token'] = $token;
        $result = [
            'code' => 0,
            'msg'  => '',
        ];
        try {
            $result['code'] = 1;
            $result['token'] = Firebase\JWT\JWT::encode($data, $key,$hash);
        } catch(Firebase\JWT\SignatureInvalidException $e) { //签名不正确
            $result['msg']="签名不正确";
        }catch(Firebase\JWT\BeforeValidException $e) { // 签名在某个时间点之后才能用
            $result['msg']="token失效";
        }catch(Firebase\JWT\ExpiredException $e) { // token过期
            $result['msg']="token失效";
        }catch(Exception $e) { //其他错误
            $result['msg']="未知错误";
        }
        return $result;
    }
}

if(!function_exists('decode_jwt')) {
    /**
     * jwt解密
     * @param mixed $token 用户token
     * @return array
     */
    function decode_jwt($token)
    {
        if (strlen($token) < 1) exception(50014,'无法获取用户TOKEN，请重新登录！');
        $key = env('jwt.key', '');          // 加密密钥
        $hash = env('jwt.hash','HS256');
        $result = [
            'code' => 0,
            'msg'  => '',
        ];
        try{
            Firebase\JWT\JWT::decode($token, $key,array($hash));
            $result['code'] = 1;
            $result['data'] = Firebase\JWT\JWT::decode($token, $key,array($hash));
            return $result;
        } catch(Firebase\JWT\SignatureInvalidException $e) { //签名不正确
            $result['msg'] = "签名不正确";
        }catch(Firebase\JWT\BeforeValidException $e) { // 签名在某个时间点之后才能用
            $result['msg'] = "token失效";
        }catch(Firebase\JWT\ExpiredException $e) { // token过期
            $result['msg'] = "token失效";
        }catch(Exception $e) { //其他错误
            $result['msg'] = "未知错误";
        }
        return $result;
    }
}

if(!function_exists('check_phone')) {
    /**
     * 检查手机号码
     * @param mixed $token 用户token
     * @return bool
     */
    function check_phone($tel)
    {
        if (preg_match('/^0?(13|14|15|17|18)[0-9]{9}$/',$tel)){
            return true;
        }
        return false;
    }
}

if(!function_exists('order_no')) {
    /**
     * 生成订单号
     * @return string
     */
    function order_no()
    {
        return date('YmdHis') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}

if(!function_exists('http_request')) {
    /**
     * http请求
     * @param  [type]  $url       [请求URL]
     * @param  [type]  $data      [发送数据]
     * @param  [type]  $method    [发送方式]
     * @param  [type]  $timeout   [超时时间]
     * @param  [type]  $json      [请求头为json]
     * @return [type]             [响应结果]
     */
    function http_request($url, $data=array(), $method="GET", $timeout=20000, $json=0,$header=[]){
        //初始化curl
        $curl = curl_init();

        if(strtoupper($method) == "POST"){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if(strtoupper(substr($url, 0, 5)) == "HTTPS"){
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 检查证书中是否设置域名（为0也可以，就是连域名存在与否都不验证了）
        }

        //设置选项，包括URL
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_NOSIGNAL, 1);    //注意，毫秒超时一定要设置这个
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeout);  //超时毫秒，cURL 7.16.2中被加入。从PHP 5.2.3起可使用
        curl_setopt($curl, CURLOPT_USERAGENT, "com");    //伪造UA信息
        if($json){
            curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge(array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )));
        }
        if($header){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    if(!function_exists('get_nav')) {
        /**
         * 封装路由
         * @param  [string]  $route       [请求路由]
         * @return [string]             [响应结果]
         */
        function get_nav($route = ''){
            $url =  env('app.url','http://www.green6d.com');
            $route = trim($route, '/');
            if (!empty($route)) $route = "{$route}/";
            return "{$url}/{$route}";
        }
    }

    if(!function_exists('get_route')) {
        /**
         * 封装文章路由
         * @param  [int]        $id       [请求id]
         * @param  [string]  $route       [请求路由]
         * @return [string]             [响应结果]
         */
        function get_detail($id = 0, $route = ''){
           $url =  env('app.url','http://www.green6d.test');
           return "{$url}/{$route}_{$id}.html";
        }
    }

    if(!function_exists('get_border')) {
        /**
         * 过滤表格边框
         * @param  [int]        $id       [请求id]
         * @param  [string]  $route       [请求路由]
         * @return [string]             [响应结果]
         */
        function get_border($str = ''){
            return preg_replace('/border="1"/', '', $str);
        }
    }
    if(!function_exists('get_feed')) {
        /**
         * 过滤表格边框
         * @param  [int]        $id       [请求id]
         * @param  [string]  $route       [请求路由]
         * @return [string]             [响应结果]
         */
        function get_feed($str = ''){
            return preg_replace("/\n/", "<br>", $str);
        }
    }
    if (!function_exists('security_file')) {
        /**
         * 目录路径安全过滤
         * @param mixed $data 路径
         * @return string
         */
        function security_file($data)
        {
            $pat = ['/\.{1,}/', '/\/{1,}/', '/\\{1,}/', '/\.\//', '/\.\\\/', '/ /', '/　/', '/\n/', '/\r/', '/\t/'];
            $rep = ['.', '/', '\\', '', '', '', '', '', '', ''];
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    $data[$k] = $v;
                    if (is_string($v)) {
                        $data[$k] = trim(preg_replace($pat, $rep, $v), '/');
                    }
                }
            } else {
                $data = trim(preg_replace($pat, $rep, $data), '/');
            }
            return str_replace('\\', '/', $data);
        }
    }
    if (!function_exists('del_dir_all_spe')) {
        //删除目录，特殊，当目录下面有以：‘0’ 命名的文件夹时，根本不能发生删除，所以需要这个函数来进行
        function del_dir_all_spe($dir) {
            $dh = opendir($dir);
            $dir0 = $dir . "/0";
            if (is_dir($dir0)) {
                del_dir_all_spe($dir0);
            }
            while ($file = readdir($dh)) {
                if ($file != "." && $file != "..") {
                    $full_path = $dir . DIRECTORY_SEPARATOR . $file;
                    if (!is_dir($full_path)) {
                        unlink($full_path);
                    } else {
                        del_dir_all_spe($full_path);
                    }
                }
            }
            closedir($dh);
            if (rmdir($dir)) {
                return true;
            } else {
                return false;
            }
        }
    }
    if (!function_exists('del_dir')) {
        // 删除目录
        function del_dir($dir) {
            $dh = opendir($dir);
            while (false !== ($file = readdir($dh)) ) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . DIRECTORY_SEPARATOR . $file;
                    if (!is_dir($fullpath)) {
                        unlink($fullpath);
                    } else {
                        del_dir($fullpath);
                    }
                }
            }
            closedir($dh);
            if (rmdir($dir)) {
                return true;
            } else {
                return false;
            }
        }
    }
    }

    if(!function_exists('is_mobile')) {
        //判断是手机登录还是电脑登录
        function is_mobile() {
            // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
            if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
                return true;
            //此条摘自TPM智能切换模板引擎，适合TPM开发
            if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
                return true;
            //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
            if (isset ($_SERVER['HTTP_VIA']))
                //找不到为flase,否则为true
                return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
            //判断手机发送的客户端标志,兼容性有待提高
            if (isset ($_SERVER['HTTP_USER_AGENT'])) {
                $clientkeywords = array(
                    'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
                );
                //从HTTP_USER_AGENT中查找手机浏览器的关键字
                if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                    return true;
                }
            }
            //协议法，因为有可能不准确，放到最后判断
            if (isset ($_SERVER['HTTP_ACCEPT'])) {
                // 如果只支持wml并且不支持html那一定是移动设备
                // 如果支持wml和html但是wml在html之前则是移动设备
                if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                    return true;
                }
            }
            return false;
        }
    }


    if(!function_exists('get_file')) {
        //判断是手机登录还是电脑登录
        function get_file($file_url, $width = 0, $height = 0)
        {
            // 过滤路径
            $old_url = security_file($file_url);
            // 判断文件是否存在
            $file_url = \think\facade\Filesystem::disk('public')->path($old_url);
            if (!file_exists($file_url)) exception('原文件不存在');
            if (empty($width) || empty($height)) return download($file_url, '')->force(false);
            // 根据图片后缀分割
            $explode_url = explode('.',$file_url);
            // 拼装新的文件名称
            $file_view_url = "{$explode_url[0]}_{$width}_{$height}.{$explode_url[1]}";
            if (!file_exists($file_view_url)) {
                $image = \think\Image::open($file_url);
                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                $image->thumb($width, $height)->save($file_view_url);
            }
            // 直接显示文件
            return download($file_view_url, '')->force(false); //force传false查看图片，传true下载图片;
        }
    }


if(!function_exists('replace_all')) {
    //判断是手机登录还是电脑登录
    function replace_all($str){
        return str_replace('src=', 'data-src=', $str);
    }
}