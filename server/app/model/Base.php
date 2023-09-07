<?php
namespace app\model;

use think\db\exception\DbException as Exception;
use think\Model;
use think\Paginator;

class Base extends Model
{

    // 拼装结束
    public function getPathUrlAttr($value, $data)
    {
        $path_url =[];
        // 拼装返回信息
        $host = is_https().env('app.host',$_SERVER['HTTP_HOST']);
        $pathArr = json_decode($data['path'],true);

        if (!empty($pathArr)) {
            foreach($pathArr as $item) {
                $path_url[] = "{$host}/getFile?file_url={$item['path']}";
            }
        }
        return $path_url;
    }
}