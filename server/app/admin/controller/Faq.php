<?php

namespace app\admin\controller;

use app\model\Faq as FaqModel;
use app\common\controller\Common;
use think\facade\Db;
use app\common\exception\BaseException;

class Faq extends Common
{
    // 获取问题
    public function getIndex(FaqModel $faqModel)
    {
        try {

            $title = input('title', '', 'trim');

            $where  = [];
            if (strlen($title))  $where[] = ['title', 'like', "%{$title}%"];

            $list   = $faqModel->where($where)->order('update_time desc')
                ->page($this->page)->limit($this->limit)
                ->select();
            $total  = $faqModel->where($where)->count();
            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                    'total'         => $total,
                ],
                'msg'     =>  '获取问题成功！'
            ];
        } catch (\Exception $th) {
            $result = [
                'code'  =>  $th->getCode(),
                'msg'   =>  $th->getMessage()
            ];
        }
        throw new BaseException($result);
    }

    // 保存问题
    public function saveIndex(FaqModel $faqModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $data = input('post.');
            $action = $data['id'] == 0 ? '添加问题' : '修改问题';
            $id = $data['id'];
            unset($data['formTitle']);
            unset($data['count']);
            // id为0时，视为添加数据
            $data['update_time'] = isset($data['update_time']) ? strtotime($data['update_time']) : time();
            unset($data['id']);
            if ($id == 0) {
                $data['create_time'] = time();
                $faqModel->insert($data);
            } else {
                unset($data['create_time']);
                $faqModel->where('id',$id)->update($data);
            }
            $result = [
                'code'  => 20000,
                'msg'   => '保存问题数据成功！'
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

    // 删除问题 需要修改
    public function delIndex(FaqModel $faqModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $id = input('id', [], 'trim');
            if (!count($id)) exception('请选择删除的问题！');
            // 删除问题数据
            $faqModel->destroy($id);

            $result = [
                'code'  => 20000,
                'msg'   => '删除问题数据成功！'
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
        $this->addPlay($result, '删除问题');
        throw new BaseException($result);
    }

}
