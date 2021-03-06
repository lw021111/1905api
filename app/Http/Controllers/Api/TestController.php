<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    // function test(){
    // 	echo '<pre>';print_r($_SERVER);echo '</pre>';
    // }

    //用户注册
    function reg0(Request $request){
    	echo '<pre>';print_r(request()->input());echo '</pre>';
    	$pass1 = request()->input('pass1');
    	$pass2 = request()->input('pass2');
    	if($pass1 != $pass2){
    		die("两次输入的密码不一致");
    	}

    	$password = password_hash($pass1,PASSWORD_BCRYPT);
    	$data = [
    		'email' =>request()->input('email'),
    		'name' => request()->input('name'),
    		'password' =>$password,
    		'mobile' =>request()->input('mobile'),
    		'last_login' =>time(),
    		'last_ip' =>$_SERVER['REMOTE_ADDR'], //获取远程IP
    	];
    	$uid=UserModel::insertGetId($data);
    	var_dump($uid);die;
    }

    //用户登录接口
    function login0(Request $request){
    	$name = request()->input('name');
    	$pass = request()->input('pass');
    	//echo "pass: ".$pass;echo '</br>';
    	$u=UserModel::where(['name'=>$name])->first();
    	if($u){
    		//echo '<pre>';print_r($u->toArray());echo '</pre>';
    		//验证密码
    		if(password_verify($pass,$u->password)){
    			//登陆成功
    			echo "登陆成功";
    			//生成token
    			$token = str::random(32);
    			$response = [
    				'errno'=>0,
    				'msg'=>'ok',
    				'data'=>[
    					'token'=>$token
    				]
    			];
    		}else{
    			$response = [
    				'errno'=> 400003,
    				'msg'=> '密码不正确'
    			];
    		}
    	}else{
    		$response = [
    			'errno'=> 400004,
    			'msg'=> '用户不存在'
    		];
    	}
    	return $response;
    }

    
   
   //APP注册
    function reg(){
        //请求admin 
        $url = 'http://admin.com/reg';
        $response = UserModel::curlPost($url,$_POST);
        return $response;
    }

    //APP登陆
    function login(){
        echo '<pre>';print_r($_POST);echo '</pre>';
        //请求admin 
        $url = 'http://admin.com/login';
        $response = UserModel::curlPost($url,$_POST);
        echo '<pre>';print_r($response);echo '</pre>';die;
        return $response;
    }
    function showData(){
        $uid=$_SERVER['HTTP_UID'];
        $token=$_SERVER['HTTP_TOKEN'];
        // echo 'UID: '.$uid;echo '</br>';
        // echo 'TOKEN: '.$token;echo '</br>';
        $url='http://admin.com/auth';
        $response = UserModel::curlPost($url,['uid'=>$uid,'token'=>$token]);
        $status=json_decode($response,true);
        if($status['error']==0){
            $data="1decde440cdc737dfba6";
            $response=[
                'error'=>0,
                'msg'=>'ok',
                'data'=>$data
            ];
        }else{
            $response=[
                'error'=>40003,
                'msg'=>'授权失败', 
            ];
        }
        return $response;
    }

    /**
     * 获取用户列表 
     */
    public function UserList()
    {
        print_r($_SERVER);die;
        $user_token=$_SERVER['HTTP_POSTMAN_TOKEN'];
        $current_url=$_SERVER['REQUEST_URI'];
        $redis_key='str:count:url:'.md5($current_url);
        // echo 'redis key:'.$redis_key;echo '<hr>';
        // 取出计数 访问次数
        $count=Redis::get($redis_key);
        echo "接口访问的次数".$count;echo '<hr>';
        // 判断最多访问次数
        if($count>=10){
            echo "访问次数已达到次数，请不要频繁刷新此接口，稍后再试。";
            Redis::expire($redis_key,30);die;
        }
        // 计数 存入redis
        $count=Redis::incr($redis_key);
        // echo 'count:'.$count;
    }

function brush(){
        $data=[
            'user_name'=>'zhangsan',
            'email'=>'zhangsan@qq.com',
            'amount'=>10000
        ];
        echo json_encode($data);
        // //获取用户标识
        // $token = $_SERVER['HTTP_TOKEN'];
        // $request_uri=$_SERVER['REQUEST_URI'];
        // $url_hash=md5($token . $request_uri);
        // //echo 'url_hash: ' . $url_hash;echo '</br>';
        // $key='count:url:'.$url_hash;
        // //echo 'key:' .$key;echo '</br>';
        // //检查 次数是否已经超过限制
        // $count=Redis::get($key);
        // echo "当前接口访问次数为: ".$count;echo '<br>';
        // if($count>=5){
        //     $time=60;
        //     echo "请勿频繁请求, $time 秒后重试";
        //     Redis::expire($key,$time);die;
        // }
        // //访问数
        // $count=Redis::incr($key);
        // echo 'count: '.$count;
    }

    function md5(){
        $data="Hello world"; //要发送的数据
        $key="1905"; //计算签名key

        //计算签名 MD5($data.$key)
        $signature=md5($data.$key);
        echo "待发送的数据:".$data;echo "</br>";
        echo "签名:".$signature;echo "</br>";

        //发送数据
        $url="http://admin.com/check?data=".$data . '&signature='.$signature;
        echo $url;echo "<hr>";

        $response=file_get_contents($url);
        echo $response;
    }

    public function md5test()
    {
        $key = "yangtao";          

        $order_info = [
            "order_id"          => 'LN_' . mt_rand(111111,999999),
            "order_amount"      => mt_rand(111,999),
            "uid"               => 12345,
            "add_time"          => time(),
        ];

        $data_json = json_encode($order_info);

        //计算签名
        $sign = md5($data_json.$key);

        // post 表单（form-data）发送数据
        $client = new Client();
        $url = 'http://admin.com/test/md5test2';
        $response = $client->request("POST",$url,[
            "form_params"   => [
                "data"  => $data_json,
                "sign"  => $sign
            ]
        ]);

        //接收服务器端响应的数据
        $response_data = $response->getBody();
        echo $response_data;
    }   

    //私钥签名
    function sign3(){
        $data="Hello world";//待签名数据

        //计算签名
        $path=storage_path('keys/privkey3');
        $pkeyid=openssl_pkey_get_private("file://".$path);

        openssl_sign($data,$signature,$pkeyid);
        openssl_free_key($pkeyid);

        //base64编码
        $sign_str=base64_encode($signature);
        echo 'base64_encode 后的签名:'.$sign_str;echo "</br>";
        $url="http://admin.com/test/sign3?".'data='.$data.'&sign_str='.urlencode($sign_str);
        echo $url;
    }

    // function encrypt(){
    //     $data="啦啦啦";
    //     $path=storage_path('keys/privkey3');
    //     $prive_key=openssl_pkey_get_private("file://".$path);
    //     openssl_private_encrypt($data, $encrypt_data, $prive_key,OPENSSL_PKCS1_PADDING);
    //     var_dump($encrypt_data);echo "<hr>";

    //     $base64_str=base64_encode($encrypt_data);
    //     echo '</br>';
    //     echo $base64_str;

    // }

    function encrypt(){
        $data=[
            'name' => 'liuwei',
            'email' => '2841732297@qq.com',
            'age' => 17
        ];
        echo '<pre>';print_r($data);echo '</pre>';
        $json_str=json_encode($data);
        echo "原文: ".$json_str;echo '</br>';
        //加密
        $method ='AES-256-CBC';
        $key='1905api';
        $iv='WUSD8796IDjhkchd';
        $enc_data=openssl_encrypt($json_str, $method, $key,OPENSSL_RAW_DATA,$iv);
        echo "加密后密文: ".$enc_data;echo '</br>';
        //base64encode 密文
        $base64_str = base64_encode($enc_data);
        echo "base64_str: ".$base64_str;echo '</br>';

        //url_encode
        $url_encode_str = urlencode($base64_str);
        echo '$url_encode_str : '.$url_encode_str;echo '</br>';
        //发送加密数据
        $url="http://admin.com/decrypt?data=".$url_encode_str;
        echo $url;echo '</br>';
        $response=file_get_contents($url);
        echo $response;
    }


//获取用户列表
    // function userlist(){
    //    $list=UserModel::all();
    //    echo '<pre>';print_r($list->toArray());echo '</pre>';
    // }

    // function sign1(){
    //     echo '<pre>';print_r($_GET);echo '</pre>';

    //     $sign = $_GET['sign'];
    //     unset($_GET['sign']);
    //     ksort($_GET);
    //     echo '<pre>';print_r($_GET);echo '</pre>';
    //     //拼接字符串
    //     $str="";
    //     foreach($_GET as $k=>$v){
    //         $str .= $k . '=' . $v . '&';
    //     }
    //     $str=rtrim($str,'&');
    //     echo $str;echo '<hr>';

    //     //使用公钥验签
    //     $pub_key=file_get_contents(storage_path('keys/pubkey2'));
    //     $status=openssl_verify($str, base64_decode($sign),$pub_key,OPENSSL_ALGO_SHA256);
    //     var_dump($status);

    //     if($status){
    //         echo "success";
    //     }else{
    //         echo "验签失败";
    //     }
    // }

    // function sign2(){
    //     $sign_token='abcdefg';
    //     echo '<pre>';print_r($_GET);echo '</pre>';
    //     //保存sign
    //     $sign1=$_GET['sign'];
    //     echo "发送端的签名:  ".$sign1;echo '</br>';
    //     unset($_GET['sign']);

    //     ksort($_GET);
    //     echo '<pre>';print_r($_GET);echo '</pre>';
    //     //拼接待签名字符串
    //     $str="";
    //     foreach($_GET as $k=>$v){
    //         $str .= $k . '=' . $v . '&';
    //     }
    //     $str=rtrim($str,'&');
    //     echo "待签名字符串: ". $str;
    //     echo '</br>';
    //     //计算签名
    //     $sign2=sha1($str.$sign_token);
    //     echo '</br>';
    //     echo "接收端计算的签名: ". $sign2;
    //     echo '</br>';
    //     if($sign1===$sign2){
    //         echo "验签成功";
    //     }else{
    //         echo "验签失败";
    //     }
    // }

    // //自动上线
    // function gitpull(){
    //     $cmd = 'cd /wwwroot/1905-api && git pull';
    //     shell_exec($cmd);
    // }
}
