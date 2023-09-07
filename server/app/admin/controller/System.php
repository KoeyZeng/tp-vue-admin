<?php
namespace app\admin\controller;

use app\common\controller\Common;
use app\model\AdminMenu;
use app\model\AdminRole;
use think\facade\Db;
use app\model\AdminUser;
use think\facade\Cache;
use app\common\exception\BaseException;

class System extends Common
{
    // 获取管理员列表
    public function getAdmin()
    {
        try {
            $username = input('username','','trim');
            $where  = [];
            if ($username !== null && $username !== '') array_push($where, ['username', 'like', '%' . $username . '%']);
            $list   = AdminUser::where($where)->order('id desc')
            ->page($this->page)->limit($this->limit)->select();
            $total  = AdminUser::where($where)->count();
            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                    'total'         => $total,
                ],
                'msg'     =>  '获取管理员列表成功！'
            ];
        } catch (\Exception $th) {
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        throw new BaseException($result);
    }

    // 保存管理员信息
    public function saveAdmin()
    {
        // 启动事务
        DB::startTrans();
        try {
            $data = input('post.');
            $action = $data['id'] == 0 ? '添加管理员' : '修改管理员';
            unset($data['title']);
            $id = $data['id'];
            unset($data['id']);
            // id为0时，视为添加数据
            if($id == 0){
                if(!strlen($data['password'])) exception('请输入您的密码！');
                $count = AdminUser::where('username',$data['username'])->count();
                if($count > 0) exception('该账号已存在，请重新输入！');
//                $count = AdminUser::where('phone',$data['phone'])->count();
//                if($count > 0) exception('该手机已存在，请重新输入！');
                // 密码加密
                $data['password'] = password($data['password']);

                AdminUser::insert($data);
            } else {
                if(strlen($data['password'])){
                    // 密码加密
                    $data['password'] = password($data['password']);
                    // 修改了密码需要重新登录
                    $data['token'] = '';
                }else{
                    unset($data['password']);
                }
                AdminUser::where('id',$id)->update($data);
            }

            $result = [
                'code'  => 20000,
                'msg'   => '保存管理员数据成功！'
            ];
            // 提交事务
            DB::commit();
        } catch (\Exception $th) {
            // 回滚事务
            DB::rollback();
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        $this->addPlay($result,$action);
        throw new BaseException($result);
    }

    // 删除管理员
    public function delAdmin()
    {
         // 启动事务
         DB::startTrans();
         try {
            $id = input('id',0,'trim');
            if(empty($id)) exception('请选择删除的管理员！');
            if($id === 1) exception('超级管理员无法被删除！');
            AdminUser::destroy($id);
            $result = [
                'code'  => 20000,
                'msg'   => '删除管理员数据成功！'
            ];
            // 提交事务
            DB::commit();
         } catch (\Exception $th) {
            // 回滚事务
            DB::rollback();
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
         }
         $this->addPlay($result,'删除管理员');
         throw new BaseException($result);
    }

    // 修改管理员密码
    public function changePassword()
    {
        // 启动事务
        DB::startTrans();
        try {
            $password           = input('post.password','','trim');
            $new_password       = input('post.new_password','','trim');
            $confirm_password   = input('post.confirm_password','','trim');

            if(password($password) !== $this->admin['password']) exception('输入原密码不正确！');

            if(strlen($new_password) < 6) exception('新密码字符长度不能小于6！');
            if(strlen($confirm_password) < 6) exception('再次输入新密码字符长度不能小于6！');
            if($confirm_password !== $new_password) exception('两次输入的密码不一致！');

            AdminUser::where('id',$this->admin['id'])->update([ 'password' => password($new_password) ]);
            $result = [
                'code'  => 20000,
                'msg'   => '修改密码成功！'
            ];
            // 提交事务
            DB::commit();
        } catch (\Exception $th) {
            // 回滚事务
            DB::rollback();
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        $this->addPlay($result,'修改密码');
        throw new BaseException($result);
    }

    // 获取菜单列表
    public function getMenu()
    {
        try {
            $list   = $this->generateTree(AdminMenu::order('sort_id asc')->select()->toArray());
            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                ],
                'msg'     =>  '获取菜单列表成功！'
            ];
            
        } catch (\Exception $th) {
            $result = [
                'code'  =>  $th->getCode(),
                'msg'   =>  $th->getMessage()
            ];
        }
        throw new BaseException($result);
    }

    // 保存菜单列表
    public function saveMenu()
    {
        // 启动事务
        DB::startTrans();
        try {
            $data = input('post.');
            $action = $data['id'] == 0 ? '添加菜单' : '修改菜单';
            unset($data['title']);
            $id = $data['id'];
            unset($data['id']);
            // id为0时，视为添加数据
            $data['component'] = ltrim($data['component'],'/');
            if($id == 0) {
                AdminMenu::insert($data);
            } else {
                AdminMenu::where('id',$id)->update($data);
            }

            $result = [
                'code'  => 20000,
                'msg'   => '保存菜单数据成功！'
            ];
            // 提交事务
            DB::commit();
        } catch (\Exception $th) {
            // 回滚事务
            DB::rollback();
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        $this->addPlay($result,$action);
        throw new BaseException($result);
    }

    // 删除菜单列表
    public function delMenu()
    {
        // 启动事务
        DB::startTrans();
        try {
            $id = input('id',0,'trim');
            if(empty($id)) exception('请选择删除的菜单！');
            $count = AdminMenu::where('pid',$id)->count();
            if($count > 0) exception('请先删除该菜单的子菜单！');
            AdminMenu::destroy($id);
            $result = [
                'code'  => 20000,
                'msg'   => '删除菜单数据成功！'
            ];
            // 提交事务
            DB::commit();
        } catch (\Exception $th) {
            // 回滚事务
            DB::rollback();
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        $this->addPlay($result,'删除菜单');
        throw new BaseException($result);
    }
    
    // 获取角色列表
    public function getRole()
    {
        try {
            $name = input('name','','trim');
            $where  = [];
            if ($name !== null && $name !== '') array_push($where, ['name', 'like', '%' . $name . '%']);
            $list   = AdminRole::where($where)->order('id desc')->select();
            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                ],
                'msg'     => '获取角色列表成功！'
            ];
            
        } catch (\Exception $th) {
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        throw new BaseException($result);
    }

    // 保存角色列表
    public function saveRole()
    {
        // 启动事务
        DB::startTrans();
        try {
            $data = input('post.');
            $action = $data['id'] == 0 ? '添加角色' : '修改角色';
            unset($data['title']);

            $id = $data['id'];
            unset($data['id']);
            // id为0时，视为添加数据
            if ($id == 0) {
                AdminRole::insert($data);
            } else {
                AdminRole::where('id',$id)->update($data);
            }

            $result = [
                'code'  => 20000,
                'msg'   => '保存角色数据成功！'
            ];
            // 提交事务
            DB::commit();
        } catch (\Exception $th) {
            // 回滚事务
            DB::rollback();
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        $this->addPlay($result,$action);
        throw new BaseException($result);
    }
    
    // 删除角色列表
    public function delRole()
    {
        // 启动事务
        DB::startTrans();
        try {
            $id = input('id',0,'trim');
            if(empty($id)) exception('请选择删除的角色！');
            if($id === 1) exception('超级管理员角色无法被删除！');

            AdminRole::destroy($id);
            $result = [
                'code'  => 20000,
                'msg'   => '删除角色数据成功！'
            ];
            // 提交事务
            DB::commit();
        } catch (\Exception $th) {
            // 回滚事务
            DB::rollback();
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        $this->addPlay($result,'删除角色');
        throw new BaseException($result);
    }

    // 获取登录日志
    public function getLogin()
    {
        $this->getLog('admin_login');
    }

    // 获取操作日志
    public function getPlay()
    {
        $this->getLog('admin_play');
    }

    // 清理缓存
    public function getClear()
    {
        try {
            // 清理缓存文件
            $clear = Cache::clear();
            if(!$clear) exception('清理缓存失败！');

            $result = [
                'code'  => 20000,
                'msg'   => '清理缓存成功！'
            ];
        } catch (\Exception $th) {
            // 回滚事务
            DB::rollback();
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage()
            ];
        }
        $this->addPlay($result,'清理缓存');
        throw new BaseException($result);
    }

    /**
    * 获取日志
    * @param mixed $table 数据表名
    * @return string
    */
    protected function getLog($table = 'admin_login')
    {
        try {
            $username = input('get.username','','trim');
            $rang_date = input('get.rang_date','','trim');
            
            $where  = [];
            if (strlen($username)) array_push($where, ['username', 'like', '%' . $username . '%']);
            if (!empty($rang_date) && count($rang_date) === 2) array_push($where, ['create_time', 'between', [strtotime($rang_date[0]),strtotime($rang_date[1])]]);

            $list   = Db::name($table)->where($where)
            ->withAttr('create_time', function($value, $data) {
                return date('Y-m-d H:i:s',$value);
            })
            ->order('create_time', 'desc')->page($this->page)->limit($this->limit)->select();
            $total  = Db::name($table)->where($where)->count();
            // 拼装日志信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                    'total'         => $total,
                ],
                'msg'     => $table == 'admin_login'?'获取登录日志成功！':'获取操作日志成功！'
            ];
        } catch (\Exception $th) {
            $result = [
                'code'  => $th->getCode(),
                'msg'   =>  $th->getMessage() 
            ];
        }
        throw new BaseException($result);
    }
}
