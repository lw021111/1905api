<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
class checkToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //echo "鉴权中间件,请求admin实现鉴权";echo "</br>";;
        //鉴权,验证token是否有效
        $uid=$_SERVER['HTTP_UID'];
        $token=$_SERVER['HTTP_TOKEN'];

        $client = new Client();
        $response = $client->request('POST', 'http://admin.com/auth', [
                'form_params' => [
                    'uid' => $uid,
                    'token' => $token, 
                ]
            ]);
        //接收请求响应
        $response_data=$response->getBody();
        // echo $response_data;die;
        $arr=json_decode($response_data,true);

        //判断鉴权是否成功
        if($arr['error']>0){
            echo "鉴权失败";die;
        }


        return $next($request);
    }
}
