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
Route::get('api/user/:keyword', 'api/User/getUser');
Route::get('api/user', 'api/User/getUser');
Route::delete('api/user', 'api/User/delUser');
Route::patch('api/user', 'api/User/modify');

Route::get('api/test', 'api/Token/test');
Route::get('api/cache/:name', 'api/ApiTest/getCache');
