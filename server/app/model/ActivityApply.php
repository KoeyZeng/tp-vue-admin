<?php
namespace app\model;

use think\model\concern\SoftDelete;

class ActivityApply extends Base
{
    // 软删除功能
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0; // 值大于0已经删除
}