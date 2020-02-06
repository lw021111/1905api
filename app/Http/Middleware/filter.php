<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class filter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if(isset($_SERVER['HTTP_TOKEN'])){

            $redis_key='str:count:u:'.$_SERVER['HTTP_TOKEN'].':url:'.$_SERVER['REQUEST_URI'];
            $count=Redis::get($redis_key);
            if($count>10){
                Redis::expire($redis_key,60);
                $response=[
                    'errno'=>400004,
                    'msg'=>"请求接口已达上限,请稍后再试"
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }
            //计数
            Redis::incr($redis_key);
        }else{
            $response=[
                'errno'=>400003,
                'msg'=>"未授权"
            ];
            die(json_encode($response));
        }
        return $next($request);
    }
}
