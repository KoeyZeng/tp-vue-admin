<?php

namespace app\admin\controller;

use app\model\Activity as ActivityModel;
use app\model\ActivityApply as ActivityApplyModel;
use app\common\controller\Common;
use think\facade\Db;
use app\common\exception\BaseException;

class Activity extends Common
{
    // 获取活动
    public function getActivity(ActivityModel $activityModel)
    {
        try {

            $title = input('title', '', 'trim');

            $where  = [];
            if (strlen($title))  $where[] = ['title', 'like', "%{$title}%"];

            $list   = $activityModel->where($where)->order('update_time desc')
                ->withAttr('start_time', function($value, $data) {
                    return !empty($value) ? date('Y-m-d',$value) : date('Y-m-d');
                })
                ->withAttr('end_time', function($value, $data) {
                    return !empty($value) ? date('Y-m-d',$value) : date('Y-m-d');
                })
                ->page($this->page)->limit($this->limit)
                ->select();
            $total  = $activityModel->where($where)->count();
            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                    'total'         => $total,
                ],
                'msg'     =>  '获取活动成功！'
            ];
        } catch (\Exception $th) {
            $result = [
                'code'  =>  $th->getCode(),
                'msg'   =>  $th->getMessage()
            ];
        }
        throw new BaseException($result);
    }

    // 保存活动
    public function saveActivity(ActivityModel $activityModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $data = input('post.');
            $action = $data['id'] == 0 ? '添加活动' : '修改活动';
            $id = $data['id'];
            unset($data['formTitle']);
            unset($data['count']);
            // id为0时，视为添加数据
            $data['start_time'] = isset($data['start_time']) ? strtotime($data['start_time']) : strtotime(date('Y-m-d'));
            $data['end_time'] = isset($data['end_time']) ? strtotime($data['end_time']) : strtotime(date('Y-m-d'));
            $data['update_time'] = isset($data['update_time']) ? strtotime($data['update_time']) : time();

            unset($data['id']);
            if ($id == 0) {
                $data['create_time'] = time();
                $activityModel->insert($data);
            } else {
                $find =  $activityModel->find($id);
                // 判断是否为新上传的图片
                if ($data['path'] !== $find['path']) {
                    $this->updateFile($find['path'], $data['path']);
                }
                unset($data['create_time']);
                $activityModel->where('id',$id)->update($data);
            }
            $result = [
                'code'  => 20000,
                'msg'   => '保存活动数据成功！'
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
        $this->addPlay($result, $action);
        throw new BaseException($result);
    }

    // 删除活动 需要修改
    public function delActivity(ActivityModel $activityModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $id = input('id', [], 'trim');
            if (!count($id)) exception('请选择删除的活动！');

            $find = $activityModel->whereIn('id',$id)->column(['path']);
            // 删除上传的图片
            $this->unlinkFile($find);
            // 删除活动数据
            $activityModel->destroy($id);

            $result = [
                'code'  => 20000,
                'msg'   => '删除活动数据成功！'
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
        $this->addPlay($result, '删除活动');
        throw new BaseException($result);
    }

    // 获取报名
    public function getApply(ActivityApplyModel $applyModel)
    {
        try {
            $phone = input('phone','','trim');
            $rang_date = input('rang_date','','trim');
            $where  = [];
            if (strlen($phone)) array_push($where, ['phone', 'like', '%' . $phone . '%']);
            if (!empty($rang_date) && count($rang_date) === 2) array_push($where, ['create_time', 'between', [strtotime($rang_date[0]),strtotime($rang_date[1])]]);

            $list   = $applyModel->where($where)->order('create_time desc')
                ->page($this->page)->limit($this->limit)
                ->select();
            $total  = $applyModel->where($where)->count();

            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                    'total'         => $total,
                ],
                'msg'     =>  '获取报名成功！'
            ];
  
        } catch (\Exception $th) {
            $result = [
                'code'  =>  $th->getCode(),
                'msg'   =>  $th->getMessage()
            ];
        }
        throw new BaseException($result);
    }

    // 保存报名
    public function saveApply(ActivityApplyModel $applyModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $data = input('post.');
            $action = '审核报名';
            if (!isset($data['id']) || !count($data['id'])) exception('请选择审核的报名！');

            $applyModel->whereIn('id',$data['id'])->update(
                [
                    'audit'         =>   $data['audit'] ?? 0 ,
                    'audit_content' =>   $data['audit_content'] ?? '' ,
                ]
            );
            $result = [
                'code'  => 20000,
                'msg'   => '保存报名数据成功！'
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
        $this->addPlay($result, $action);
        throw new BaseException($result);
    }

    // 删除报名 需要修改
    public function delApply(ActivityApplyModel $applyModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $id = input('id', [], 'trim');
            if (!count($id)) exception('请选择删除的报名！');

            // 删除报名数据
            $applyModel->destroy($id);

            $result = [
                'code'  => 20000,
                'msg'   => '删除报名数据成功！'
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
        $this->addPlay($result, '删除报名');
        throw new BaseException($result);
    }


}
