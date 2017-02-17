<?php
/*
 * 操作REST类发送短信验证码
 */
namespace framework\tools;
use framework\vendor\Message\REST;
class Message
{
	//主帐号,对应开官网发者主账号下的 ACCOUNT SID
	private $accountSid;
	
	//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
	private $accountToken;
	
	//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
	//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
	private $appId;
	
	//请求地址
	//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
	//生产环境（用户应用上线使用）：app.cloopen.com
	private $serverIP;	
	
	//请求端口，生产环境和沙盒环境一致
	private $serverPort;
	
	//REST版本号，在官网文档REST介绍中获得。
	private $softVersion;
	
	//构造方法初始化属性
	public function __construct()
	{
		$this -> accountSid = $GLOBALS['config']['accountSid'];
		$this -> accountToken = $GLOBALS['config']['accountToken'];
		$this -> appId = $GLOBALS['config']['appId'];
		$this -> serverIP = $GLOBALS['config']['serverIP'];
		$this -> serverPort = $GLOBALS['config']['serverPort'];
		$this -> softVersion = $GLOBALS['config']['softVersion'];
	}
	
	/**
	 * 发送模板短信
	 * @param to 手机号码集合,用英文逗号分开
	 * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
	 * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
	 */
	function sendTemplateSMS($to,$datas,$tempId)
	{
		// 初始化REST SDK		
		$rest = new REST($this->serverIP,$this->serverPort,$this->softVersion);
		$rest->setAccount($this->accountSid,$this->accountToken);
		$rest->setAppId($this->appId);
	
		// 发送模板短信
		//echo "Sending TemplateSMS to $to <br/>";
		$result = $rest->sendTemplateSMS($to,$datas,$tempId);
		if($result == NULL ) {
			//echo "result error!";
			return false;
			//break;
		}
		if($result->statusCode!=0) {			
			//TODO 添加错误处理逻辑
			$data['status'] = 0;
			$data['msg'] = $result->statusMsg;
			return $data;
		}else{			
			//TODO 添加成功处理逻辑
			$data['status'] = 1;
			$data['msg'] = 'success';
			return $data;
		}
	}
	
}