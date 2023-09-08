<?php

namespace app\common\controller;

use app\BaseController;
use app\common\exception\BaseException;
use app\model\AdminMenu;
use app\model\AdminRole;
use think\facade\Db;
use app\model\AdminUser;
use think\facade\Filesystem;

class Common extends BaseController
{
    /**
     * 当前用户token
     * @var string
     */
    protected $token = '';

    /**
     * 当前用户信息
     * @var
     */
    protected $admin = null;

    /**
     * 页面显示数据数
     * @var
     */
    protected $page = 1;

    /**
     * 页面显示数据数
     * @var
     */
    protected $limit = 10;

    /**
     * 路由的控制器
     * @var
     */
    protected $controller = '';

    /**
     * 路由的事件
     * @var
     */
    protected $action = '';


    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     *
     */
    public function initialize()
    {

        parent::initialize();
        $this->controller = request()->controller();
        $this->action = request()->action();
        if('auth' !== strtolower($this->controller)) {
            // 校验用户token
            $this->checkToken();
            // 判断用户是否有权限访问事件
//            $this->checkRole();
        }
        // 设置该事件不执行
        if (strpos($this->action,'save') !== false || strpos($this->action,'del') !== false)
        {
            throw new BaseException([10000,'对不起，你无法操作该事件！']);
        }
    }

    /**
     * @Author: ZWJ
     * @msg:  校验token有效性
     * @param {type} 
     * @return: boot
     */   
    protected function checkToken($token = '')
    {

        $this->page          = input('page', env('database.page'), 'trim');
        $this->limit         = input('limit', env('database.limit'), 'trim');
        $this->token = empty($token) ? request()->header('X-Token') : $token;
        if(empty($this->token)) throw new BaseException([50014,'无法获取用户Token，请重新登录！']); 
        // 解码token
        $token = decode_jwt($this->token);
        if($token['code'] !== 1)  throw new BaseException([50014,'用户Token无效"'.$token['msg'].'"，请重新登录！']);
        $token_data = $token['data'];
        $this->admin = AdminUser::find($token_data->uid);
        // 匹配用户Token
        if ($this->admin['token'] !== $this->token) throw new BaseException([50014,'用户Token匹配不正确，请重新登录！']);
        // 匹配用户登录IP地址，
        if ($this->admin['uip']!== $token_data->uip) throw new BaseException([50014,'检查到登录IP地址异常，请重新登录！']);
    }

     /**
     * @Author: ZWJ
     * @msg:  判断用户是否有权限访问事件
     * @param {type} 
     * @return: boot
     */  
    protected function checkRole()
    {   
        $roleId = $this->admin['role'];
        // role id 为 0 是超级管理员，获取全部权限路由
        if ($roleId === false || $roleId == '1')  return true; 
        $role = AdminRole::find($roleId);
        $array=preg_split("/(?=[A-Z])/",$this->action);
        if(count($array) === 0) throw new BaseException([10000,'访问事件API格式不正确！']);
        $like = '%'.$array[1];
        $menu = count($role) > 0? explode(',',$role['menu']) : [];
        $routes = AdminMenu::whereIn('id', $menu)
        ->where('label','like',$like)->column('label');
        if(count($routes) === 0) throw new BaseException([10000,'对不起，您没有访问该接口权限！']);
    }

     /**
     * 添加操作日志
     * @return object|void $result 返回的结果信息
     * @param mixed $action 操作模块
     * @return string
     */
    protected function addPlay($result,$action = '')
    {
        $data = [
            'username'    => $this->admin['username'] ?? '',
            'action'      => $action,
            'url'         => request()->url(),
            'uip'         => get_client_ip(),
            'status'      => $result['code'] === 20000 ?1:0,
            'content'     => $result['msg'],
            'create_time' => time(),
        ];
        Db::name('admin_play')->insert($data);
    }

    /**
     * 递归实现无限极分类
     * @param $array 分类数据
     * @param $pid 父ID
     * @return $tree 树结构数据
     */
    public function generateTree($array = [], $pid = 0,$pid_arr = [])
    {
        $tree = array();
        foreach ($array as $key => $value) {
            if ($value['pid'] == $pid) {
                if($pid != 0) {
                    $dataArr = $pid_arr;
                    if($pid_arr == [0]) $dataArr = [];
                    array_push($dataArr,$pid);
                    $value['pid_arr'] = $dataArr;
                }else {
                    $value['pid_arr'] = [$pid];
                }
                $value['children'] = $this->generateTree($array, $value['id'],$value['pid_arr']);
                if (!$value['children']) unset($value['children']);
                $tree[] = $value;
            }
        }
        return $tree;
    }


    /**
     * @Author: ZWJ
     * @msg:  更新保存文件文件
     * @param {type}
     * @return: boot
     */
    protected function updateFile($oloPath = '[]', $newPath = '[]')
    {
        $oloPath = json_decode($oloPath,true);
        $newPath = json_decode($newPath,true);

        $newPathColumn = [];
        if(count($newPath)) $newPathColumn = array_column($newPath, 'path');

        foreach ($oloPath as $item)
        {
            if (!in_array($item['path'], $newPathColumn)) {
                $delPath = Filesystem::disk('public')->path($item['path']);
                // 判断是否存在文件，有就删除
                if (is_file($delPath)) @unlink($delPath);
            }
        }
        return true;
    }

    /**
     * @Author: ZWJ
     * @msg:  删除文件
     * @param {type}
     * @return: boot
     */
    protected function unlinkFile($files = [])
    {
        foreach ($files as $value) {
            $value = json_decode($value,true);
            if (!is_array($value)) continue;
            foreach ($value as $item) {
                if (isset($item['path'])) {
                    $oldPath = Filesystem::disk('public')->path($item['path']);
                    // 判断是否存在文件，有就删除
                    if (is_file($oldPath)) @unlink($oldPath);
                }
            }
        }
        return true;
    }

}
