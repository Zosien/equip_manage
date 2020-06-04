<?php
namespace app\api\model;

use think\Db;
use think\Model;

class Laboratory extends Model
{
    public static function getLabs($keyword, $scope,$page=1,$limit=20)
    {
        if ($keyword) {
            $dataSql = "select * from laboratory where name like '%".$keyword."%' limit ".$limit." offset ".($page-1)*$limit.";";
            $countSql = "select count(*) count from laboratory where name like '%".$keyword."%';";
        } else {
            $dataSql = "select * from laboratory limit ".$limit." offset ".($page-1)*$limit.";";
            $countSql = "select count(*) count from laboratory";
        }
        $data = Db::query($dataSql);
            //数据绑定有数字变字符串的问题
        $count = Db::query($countSql)[0]['count'];
        $res['dataCount'] = $count;
        $res['data'] = $data;
        return $res;
    }
}