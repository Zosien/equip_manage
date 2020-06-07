<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::post('api/token', 'api/Token/getToken');
Route::get('api/key', 'api/PublicKey/get');
Route::delete('api/token', 'api/Token/delete');
Route::get('api/refresh_token', 'api/Token/refreshToken');

/**
 * user相关
 */

Route::get('api/user/:id', 'api/User/getUserById',[],['id'=>'\d+']);
// Route::get('api/user/:keyword', 'api/User/getUserByKey',[],['keyword'=>'\w+']);
Route::get('api/user', 'api/User/getUserByKey');
Route::delete('api/user', 'api/User/delUser');
Route::patch('api/user','api/User/modifyInfo');
Route::patch('api/user/psw','api/User/changePsw');
Route::post('api/user/upload','api/User/upload');
Route::post('api/user','api/User/save');
Route::get('api/my','api/User/getInfoByToken');
Route::get('api/menu','api/User/getMenuByToken');

/**
 * equip相关
 */
// Route::get('api/equip/:keyword', 'api/Equip/getEquipByKey',[],['keyword'=>'\w+']);
Route::get('api/equip', 'api/Equip/getEquipByKey');
Route::patch('api/equip/','api/Equip/modifyInfo');
Route::delete('api/equip', 'api/Equip/delEquipByID');
Route::post('api/equip/upload','api/Equip/upload');
Route::post('api/equip','api/Equip/save');

/**
 * Lab相关
 */
Route::get('api/lab', 'api/Laboratory/getLabByKey');

/**
 * Rules
 */
Route::get('api/rules', 'api/Rules/getRules');


Route::get('api/test', 'api/ApiTest/getTest');
Route::post('api/test','api/ApiTest/postTest');
Route::get('api/cache/:name', 'api/ApiTest/getCache');

