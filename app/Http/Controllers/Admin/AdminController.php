<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserModel;
//use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class AdminController extends Controller
{
    //用户登录接口
    function login(Request $request){
    	$username = request()->input('username');
    	$password = request()->input('password');
    	//echo "pass: ".$pass;echo '</br>';
    	$info=UserModel::get();
        dd($info);die;
    	if($info){
            $pass=$info->pwd;
    		//验证密码
    		if(password_verify($password,$pass)){
    			//生成token
    			$token = str::random(32);
                Redis::expire($token,604800);
                return json_encode('code'=>'200','msg'=>"登陆成功");
            }else{
                return json_encode('code'=>'201','msg'=>"密码有误");
            }



        }
    }
}