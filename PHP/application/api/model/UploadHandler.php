<?php

namespace app\api\model;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;

class UploadHandler
{
    public static $stu_num = [];
    public static $id = [];
    public static function getUserNum()
    {
        $sql = "select distinct(stu_num) from user_info;";
        $res = Db::query($sql);
        foreach($res as $key => $val){
            foreach($val as $key => $val)
                self::$stu_num[] = $val;
        }
    }
    public static function getEquipID()
    {
        $sql = "select id from equip;";
        $res = Db::query($sql);
        foreach($res as $key => $val){
            foreach($val as $key => $val)
                self::$id[] = $val;
        }
    }
    public static function userHandler($file){
        self::getUserNum();
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $sql = "insert into user_info(institute,class,name,stu_num,gender,age) values";
        $hightestRow = $sheet->getHighestRow();
        $hightestColumn = $sheet->getHighestColumn();
        $hightestColumn = ord($hightestColumn)-64;
        $data = "";
        for($i=2;$i<=$hightestRow;$i++){
            $num = $sheet->getCellByColumnAndRow(4,$i)->getValue();
            if(!in_array($num,self::$stu_num)){
                $data .= "(";
                for($j=1;$j<=$hightestColumn;$j++){
                    $value = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                    $data .= "'$value',";
                }
                $data = rtrim($data, ",");
                $data .= "),";
            }
        }
        $data = rtrim($data, ",");
        if($data){
            $sql .= $data;
            $res = Db::table('user_info')->execute($sql);
        }
        else
            $res = 0;
        return $res;
    }
    public static function equipHandler($file){
        self::getEquipID();
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $hightestRow = $sheet->getHighestRow();
        $hightestColumn = $sheet->getHighestColumn();
        $hightestColumn = ord($hightestColumn)-64;
        $sql = "insert into equip(id,name,level,price,factory,life_span,buyer,details,lab) values";
        $data = "";
        for($i=2;$i<=$hightestRow;$i++){
            $id = $sheet->getCellByColumnAndRow(1,$i)->getValue();
            if(!in_array($id,self::$id)){
                $data .= "(";
                for($j=1;$j<=$hightestColumn;$j++){
                    $value = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                    $data .= "'$value',";
                }
                $data = rtrim($data, ",");
                $data .= "),";
            }
        }
        $data = rtrim($data, ",");
        if($data){
            $sql .= $data;
            $res = Db::table('equip')->execute($sql);
        }
        else
            $res = 0;
        return $res;
    }
}
