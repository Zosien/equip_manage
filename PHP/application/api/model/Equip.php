<?php
namespace app\api\model;

use think\Db;
use think\Model;
use think\Request;

class Equip extends Model
{
    public static function newEquip()
    {
        $req = Request::instance();
        $param = $req->param();
        changeEmptyToNull($param);
        $sql = "insert into equip(id,name,level,price,factory,life_span,buyer,lab,details) values (:id,:name,:level,:price,:factory,:life_span,:buyer,:lab,:details);";
        $res = Db::execute($sql,$param);
        return $res;
    }
    public static function getEquips($keyword, $scope,$page=1,$limit=20)
    {
        if ($keyword) {
            $dataSql = "select * from equip where status = 1 and level <= ".$scope." and name like '%".$keyword."%' limit ".$limit." offset ".($page-1)*$limit.";";
            $countSql = "select count(*) count from equip where status = 1 and level <= ".$scope." and name like '%".$keyword."%';";
        } else {
            $dataSql = "select * from equip where status = 1 and level <= ".$scope." limit ".$limit." offset ".($page-1)*$limit.";";
            $countSql = "select count(*) count from equip where status = 1 and level <= ".$scope;
        }
        $data = Db::query($dataSql);
        //数据绑定有数字变字符串的问题
        $count = Db::query($countSql)[0]['count'] ? Db::query($countSql)[0]['count'] : 0;
        $res['dataCount'] = $count;
        $res['data'] = $data;
        return $res;
    }
    public static function getEquip($id)
    {
        $res = self::where('id','in',$id)->select();
        return $res;
    }
    public static function modify($id,$options=[])
    {
        self::where('id', 'in', $id)->update($options);

        return self::getEquip($id);
    }
    /**
     * 根据id数组删除设备
     *
     * @param array $id
     *
     * @return 删除数量
     */
    public static function delEquipByID($id)
    {
        $sql = "update equip set status = 0 where id in (";
        $num = count($id);
        for($i = 0;$i<$num;$i++){
            $sql .= $id[$i].",";
        }
        $sql = rtrim($sql,',');
        $sql .= ");";
        $res = Db::execute($sql);
        return $res;
    }
}