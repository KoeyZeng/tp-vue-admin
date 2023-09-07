<?php
namespace app\model;

use think\model\concern\SoftDelete;

class Activity extends Base
{
    // 软删除功能
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0; // 值大于0已经删除

    // 拼装开始时间
    public function getStartTimeAttr($value)
    {
        return !empty($value) ? date('Y/m/d',$value) : date('Y/m/d');
    }

    // 拼装结束
    public function getEndTimeAttr($value)
    {
        return !empty($value) ? date('Y/m/d',$value) : date('Y/m/d');
    }

    // 拼装结束
    public function getHrefAttr($value, $data)
    {
        return get_detail($data['id'],'activity');
    }

    // 拼装结束
    public function getStatusAttr($value, $data)
    {
        $start_time = strtotime(date('Ymd'));
        $end_time = strtotime(date('Ymd',strtotime("+1 day")));

        if ($data['start_time'] > $start_time) {
            return '未开始';
        } else if ($data['start_time'] <= $start_time && $end_time <= $data['end_time']) {
            return '进行中';
        } else {
            return '已结束';
        }
    }
}