<?php
namespace app\admin\controller;

use app\common\controller\Common;
use app\common\exception\BaseException;
use app\model\AdminLogin;
use app\model\AdminMenu;
use app\model\AdminRole;
use app\model\AdminUser;
use think\exception\ValidateException;
use app\validate\AdminUserValidate;

class Auth extends Common
{
    // 登录限制时间 单位秒
    protected $outTime = 60 * 10 ;

    // 登录限制次数 单位秒
    protected $loginNumber = 5 ;

    // 后台用户登录接口
    public function login()
    {
        try {
            $username = input('post.username', '', 'trim');
            $password = input('post.password', '', 'trim');
            $uip = get_client_ip();
            $this->admin['username'] = $username;
            // 缓存的索引，错误失败次数
            $nameCache = "{$uip}{$username}_lock_time";
            // 判断是否禁止登录
            // 获取缓存数据
            $adminCache = cache($nameCache,'');
            if(isset($adminCache['lose']) && isset($adminCache['limit']) && ($adminCache['lose'] > time() && $adminCache['limit'] > ($this->loginNumber - 1))){
                $date = date('Y-m-d H:i:s',$adminCache['lose']);
                exception("您登录失败次数过多，请在{$date}后再试！");
            }
            // 检验提交数据
            try {
                validate(AdminUserValidate::class)->check([
                    'username'  => $username,
                    'password'  => $password
                ]);
            } catch (ValidateException $e) {
                // 设置登录次数
                $stopLogin =  $this->stopLogin($nameCache);
                exception($e->getError().','.$stopLogin);
            }

            // 检验密码是否正确
            $this->admin = AdminUser::where('username',$username)->find();

            if (empty($this->admin))  exception('用户不存在！');

            if (password($password) != $this->admin['password']) exception('密码输入有误！');
            if ($this->admin['status'] == 0) exception('账号已被禁用,请联系管理员！');
            
            // 生成用户jwt
            $jwt = encode_jwt($this->admin['id']);
            if($jwt['code'] !== 1) exception($jwt['msg']);
            $this->admin['token'] = $jwt['token'];
            $update = [
                'token' => $this->admin['token'],
                'uip'   => $uip,
            ];
            AdminUser::where('username',$username)->update($update);
            // 删除缓存数据
            cache($nameCache, NULL);
            $result = [
                'code'    => 20000,
                'data'    => ['token'=>$this->admin['token']],
                'msg'     =>  '登录成功！'
            ];
        } catch(\Exception $th) {
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        // 添加登录记录
        $this->addLogin($result,1);
        throw new BaseException($result);
    }

    // 后台用户注销接口
    public function logout()
    {         
        $this->checkToken();
        $update = [
            'token' => '',
            'uip'   => '',
        ];
        // 更新数据库
        AdminUser::where('id',$this->admin['id'])->update($update);
        $result = [
            'code'    => 20000,
            'msg'     =>  '注销成功！'
        ];
        // 添加注销记录
        $this->addLogin($result,2);
        throw new BaseException($result);
    }

    // 获取用户信息接口
    public function info()
    {
        try {
            $token = input('token');
            $this->checkToken($token);
            if(empty($this->admin)) exception('用户不存在！');
            $routes = $this->getRoutes($this->admin['role']);
            // 拼装返回信息
            $host = is_https().env('app.host',$_SERVER['HTTP_HOST']);
            $result = [
                'code'    => 20000,
                'data'    => [
                    'roles'         => [$this->admin['name']],
                    'introduction'  => $this->admin['nickname'],
                    'avatar'        => $host.$this->admin['avatar'],
                    'host'          => $host,
                    'storage'       => "{$host}/storage/",
                    'name'          => $this->admin['username'],
                    'routes'        => $routes
                ],
                'msg'     =>  '获取用户信息成功！'
            ];
        } catch (\Exception $th) {
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        throw new BaseException($result);
    }

    // 保存文件
    public function saveFile(){
        try {
            $file = request()->file('file');
            $putFile = input('putFile', 'news','trim');

            if(empty($file)) exception('请选择上传文件！');
            if ($file->getSize() > (1024* 1024 * 2)) exception('上传文件不能超过2MB！');
                // 上传文件的名称
            $file_name = $file->getOriginalName();
            // 上传到本地服务器
            $path = \think\facade\Filesystem::disk('public')->putFile($putFile, $file);
            $path = str_replace('\\','/',$path);
            // 如果上传图片超过5MB 进行压缩
            if ($file->getSize() > (1024* 1024 * 2) && in_array($file->getOriginalExtension(),['jpg', 'jpeg', 'png', 'bmp', 'wbmp','gif'])) {
                $file_url = \think\facade\Filesystem::disk('public')->path($path);
                // 获取完整图片路径
                $percent = 0.5;  #缩放比例
                $image = \think\Image::open($file_url);
                // 压缩图片
                $image->save($file_url,null, $percent);
            }

            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'path'          => "{$path}",
                    'path_url'      => is_https().env('app.host',$_SERVER['HTTP_HOST']).'/storage/'.$path
                ],
                'msg'     =>  '保存文件成功！'
            ];
        } catch(\Exception $th) {
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        throw new BaseException($result);
    }

    // 显示文件
    public function getFile()
    {
        try {
            // 文件目录
            $file_url = input('get.file_url', 'public', 'trim');
            if (empty($file_url)) exception('无法获取文件目录');
            // 校验token
            $token = input('get.token','','trim');
            if (empty($token)) exception('无法获取用户TOKEN');
            $this->checkToken($token);
            $name = input('get.name','','trim');
            // 过滤路径
            $old_url = security_file($file_url);
            // 判断文件是否存在
            $file_url = \think\facade\Filesystem::disk('public')->path($old_url);

            $name = !empty($name) ? $name : $file[1] ?? '附件';
            if (!file_exists($file_url)) exception('文件不存在');
            // 直接显示文件
            return download($file_url, $name)->force(true); //force传false查看图片，传true下载图片;
        } catch (\Exception $th) {
            $result = [
                'code' => $th->getCode(),
                'msg' => $th->getMessage()
            ];
            throw new BaseException($result);
        }
    }
    // 设置登录次数
    protected function stopLogin($nameCache)
    {
        // 获取缓存数据
        $adminCache = cache($nameCache,'');
        // 当前时间
        $nowTime = time();
        // 新建缓存数据
        if(empty($adminCache)) {
            $adminCache = [
                'lose'  => $nowTime + $this->outTime, // 失效时间
                'limit' => 1,
                'time'  => $nowTime,
            ];
        }

        $surplus = $this->loginNumber - $adminCache['limit'];

        $date = date('Y-m-d H:i:s',$adminCache['lose']);
        if ($surplus > 0 && (time() - $adminCache['lose']) < $this->outTime){
            $adminCache['limit'] ++ ;
            $msg = "您还有{$surplus}次机会！";
        }
        if ($surplus == 0 && (time() - $adminCache['lose']) < $this->outTime){
            $msg  = "您登录失败次数过多，请在{$date}后再试！";
        }
        if ($surplus == 0 && (time() - $adminCache['lose']) > $this->outTime) {
            $adminCache = [
                'lose'  => $nowTime + $this->outTime, // 失效时间
                'limit' => 1,
                'time'  => $nowTime,
            ];
            $msg = "您还有4次机会！";
        }
        // 设置缓存
        cache($nameCache,$adminCache,$adminCache['lose'] - $nowTime);
        return $msg;
    }

    // 添加登录注销日志
    protected function addLogin($result,$type = 1)
    {
        $data = [
            'username'    => $this->admin['username'] ?? '',
            'uip'         => get_client_ip(),
            'type'        => $type,             // 1-登录 2-注销
            'status'      => $result['code'] === 20000 ?1:0,
            'content'     => $result['msg'],
            'create_time' => time(),
        ];
        AdminLogin::insert($data);
    }

    // 获取用户权限路由
    protected function getRoutes($roleId = false)
    {   
        $role = AdminRole::where('id',$roleId)->find();
        $this->admin['name'] = $role['name'];
        // role id 为 0 是超级管理员，获取全部权限路由
        if ($roleId === false || $roleId == '1') {
            $routes = AdminMenu::order('sort_id asc')->select();
        } else {
            $menu = !empty($role) ? explode(',',$role['menu']) : [];
            $routes = AdminMenu::whereIn('id', $menu)->order('sort_id asc')->select();
        }
        // 将数组索引重置为连续数字
        return  $routes;
    }
}
