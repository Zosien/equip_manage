<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function getRandomChar($length)
{
    $str = null;
    $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $max = strlen($strPol) - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $strPol[rand(0, $max)];
    }

    return $str;
}
function saveFile($path,$file)
{
    $info = $file->move($path);
    if($info){
        return $info->getSaveName();
    }else{
        throw new Exception($file->getError(),500);
    }
}
function changeEmptyToNull(&$array=[])
{
    foreach($array as $key => &$val){
        if(empty($val) && $val !== 0){
            $val = null;
        }
    }
}
