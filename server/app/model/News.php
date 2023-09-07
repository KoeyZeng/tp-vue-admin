<?php
namespace app\model;

use think\model\concern\SoftDelete;

class News extends Base
{
    // 软删除功能
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0; // 值大于0已经删除

    // 拼装结束
    public function getHrefAttr($value, $data)
    {
        return get_detail($data['id'],'news');
    }

    // 拼装结束
    public function getDateAttr($value, $data)
    {
        return date('Y/m/s',$data['update_time']);
    }

}