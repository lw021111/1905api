<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::any('/api/test','Api\TestController@test');
Route::any('/api/user/reg','Api\TestController@reg'); //用户注册
Route::any('/api/user/login','Api\TestController@login'); //用户登录
Route::any('/api/user/data','Api\TestController@showData'); //用户登录

Route::any('/api/user/list','Api\TestController@userlist')->middleware('filter'); //用户列表
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::any('/sign1','Api\TestController@sign1');

Route::get('/test/get/sign1','Sign\IndexController@sign1');
Route::post('/test/post/sign2','Sign\IndexController@sign2');
Route::get('/test/sign2','Api\TestController@sign2');
Route::get('/test/sign3','Api\TestController@sign3');//私钥签名

//鉴权
Route::get('/brush','Api\TestController@brush')->middleware('filter','check.token');

Route::get('/md5','Api\TestController@md5');
Route::get('/md5test','Api\TestController@md5test');


//自动上线
Route::post('/getpull','Api\TestController@gitpull');

