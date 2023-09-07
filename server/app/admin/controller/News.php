<?php

namespace app\admin\controller;

use app\model\News as newsModel;
use app\common\controller\Common;
use think\facade\Db;
use app\common\exception\BaseException;

class News extends Common
{
    // 获取文章
    public function getArticle(newsModel $newsModel)
    {
        try {

            $title = input('title', '', 'trim');

            $where  = [];
            if (strlen($title))  $where[] = ['title', 'like', "%{$title}%"];

            $list   = $newsModel->where($where)->order('update_time desc')
                ->page($this->page)->limit($this->limit)
                ->select();
            $total  = $newsModel->where($where)->count();
            // 拼装返回信息
            $result = [
                'code'    => 20000,
                'data'    => [
                    'list'          => $list,
                    'total'         => $total,
                ],
                'msg'     =>  '获取文章成功！'
            ];
        } catch (\Exception $th) {
            $result = [
                'code'  =>  $th->getCode(),
                'msg'   =>  $th->getMessage()
            ];
        }
        throw new BaseException($result);
    }

    // 保存文章
    public function saveArticle(newsModel $newsModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $data = input('post.');
            $action = $data['id'] == 0 ? '添加文章' : '修改文章';
            $id = $data['id'];
            unset($data['formTitle']);
            unset($data['count']);
            // id为0时，视为添加数据
            $data['update_time'] = isset($data['update_time']) ? strtotime($data['update_time']) : time();
            unset($data['id']);
            if ($id == 0) {
                $data['create_time'] = time();
                $newsModel->insert($data);
            } else {
                $find =  $newsModel->find($id);
                // 判断是否为新上传的图片
                if ($data['path'] !== $find['path']) {
                    $this->updateFile($find['path'], $data['path']);
//                    $oldPath = \think\facade\Filesystem::disk('public')->path($find['path']);
//                    if (is_file($oldPath)) @unlink($oldPath);
                }
                unset($data['create_time']);
                $newsModel->where('id',$id)->update($data);
            }
            $result = [
                'code'  => 20000,
                'msg'   => '保存文章数据成功！'
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

    // 删除文章 需要修改
    public function delArticle(newsModel $newsModel)
    {
        // 启动事务
        DB::startTrans();
        try {
            $id = input('id', [], 'trim');
            if (!count($id)) exception('请选择删除的文章！');

            $find = $newsModel->whereIn('id',$id)->column('path');
            // 删除上传的图片
            $this->unlinkFile($find);
            // 删除文章数据
            $newsModel->destroy($id);

            $result = [
                'code'  => 20000,
                'msg'   => '删除文章数据成功！'
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
        $this->addPlay($result, '删除文章');
        throw new BaseException($result);
    }

}
