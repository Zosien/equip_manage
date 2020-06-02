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
Route::get('api/menu/:scope', 'api/Menu/menu');
Route::get('api/user/:id', 'api/User/getUserById',[],['id'=>'\d+']);
Route::get('api/user/:keyword', 'api/User/getUserByKey',[],['keyword'=>'\w+']);
Route::get('api/user', 'api/User/getUserByKey');
Route::delete('api/user', 'api/User/delUser');
Route::patch('api/user', 'api/User/modify');
Route::post('api/user/upload','api/User/upload');
Route::post('api/user','api/User/save');

Route::get('api/test', 'api/ApiTest/getTest');
Route::post('api/test','api/ApiTest/postTest');
Route::get('api/cache/:name', 'api/ApiTest/getCache');

