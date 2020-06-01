<?php

namespace app\api\model;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;

class UploadHandler
{
    public static $stu_num = [];

    public static function init()
    {
        $sql = "select distinct(stu_num) from user_info";
        $res = Db::query($sql);
        foreach($res as $key => $val){
            foreach($val as $key => $val)
                self::$stu_num[] = $val;
        }
    }
    public static function save($path,$file)
    {
        $info = $file->move($path);
        if($info){
            return $info->getSaveName();
        }else{
            throw new Exception($file->getError(),500);
        }
    }
    public static function handler($file){
        self::init();
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
}
