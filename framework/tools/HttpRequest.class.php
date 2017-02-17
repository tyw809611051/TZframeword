<?php
namespace framework\tools;
/*
 * HttpRequest类是用来实现网络传输一些资源 get/post
 */
class HttpRequest
{
	//是否返回结果，默认是返回结果而不是直接显示
	private $is_return = 1;
	
	public function __set($p,$v){
		if(property_exists($this, $p)){
			$this -> $p = $v;
		}
	}	
	//发出http请求（get、post）
	//既能发出get请求，又能发出post请求,如果$data是空数组就发出get请求，如果$data有数据，就发出post请求
	public function send($url,$data=array())
	{
		//1. 初始化curl
		$curl = curl_init();
		
		//2. 设置请求的服务器地址url地址		
		curl_setopt($curl, CURLOPT_URL, $url);
		//跳过证书验证这个环节
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);		
		if(!empty($data)){
			//说明提交数据了，就以post方式请求
			//post方式发出请求
			curl_setopt($curl, CURLOPT_POST, true);			
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		//3. 发出请求		
		//如果只需要返回结果，而不是直接显示
		if($this->is_return){
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($curl);
			if($result===false){
				$msg['status'] = 0;
				$msg['msg'] = curl_error($curl);
				return $msg;
			}else{
				$msg['status'] = 1;
				$msg['msg'] = $result;
				return $msg;
			}			
		}else{
			curl_exec($curl);
		}
		//4. 关闭资源
		curl_close($curl);
	}
}