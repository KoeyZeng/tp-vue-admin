<?php

namespace app\admin\controller;

use app\model\Consult as ConsultModel;
use app\common\controller\Common;
use think\facade\Db;
use app\common\exception\BaseException;

class Consult extends Common
{
    // 获取咨询
    public function getIndex(ConsultModel $consultModel)
    {
        try {

            $phone = input('get.phone','','trim');
            $rang_date = input('get.rang_date','','trim');

            $where  = [];
            if (strlen($phone)) array_push($where, ['phone', 'like', '%' . $phone . '%']);
            if (!empty($rang_date) && count($rang_date) === 2) array_push($where, ['create_time', 'between', [strtotime($rang_date[0]),strtotime($rang_date[1])]]);

            $list   = $consultModel->where($where)->order('create_time desc')
                ->page($this->page)->limit($this->limit)
                ->select();
            $total  = $consultModel->where($where)->count();
            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                    'total'         => $total,
                ],
                'msg'     =>  '获取咨询成功！'
            ];
        } catch (\Exception $th) {
            $result = [
                'code'  =>  $th->getCode(),
                'msg'   =>  $th->getMessage()
            ];
        }
        throw new BaseException($result);
    }

    // 保存咨询
    public function saveIndex(ConsultModel $consultModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $data = input('post.');
            $action = '审核咨询';
            if (!isset($data['id']) || !count($data['id'])) exception('请选择审核的咨询！');

            $consultModel->whereIn('id',$data['id'])->update(
                [
                    'audit'         =>   $data['audit'] ?? 0 ,
                    'audit_content' =>   $data['audit_content'] ?? '' ,
                ]
            );
            $result = [
                'code'  => 20000,
                'msg'   => '保存咨询数据成功！'
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

    // 删除咨询 需要修改
    public function delIndex(ConsultModel $consultModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $id = input('id', [], 'trim');
            if (!count($id)) exception('请选择删除的咨询！');

            // 删除咨询数据
            $consultModel->destroy($id);

            $result = [
                'code'  => 20000,
                'msg'   => '删除咨询数据成功！'
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
        $this->addPlay($result, '删除咨询');
        throw new BaseException($result);
    }

}
