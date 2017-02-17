<?php
namespace framework\tools;
class Verify
{
	//该属性保存错误信息
	private $error = array();
	
	//给用户显示错误信息
	public function showError()
	{
		$message = '';
		foreach ($this->error as $row)
		{
			$message .= $row.'<br>';
		}
		return $message;
	}
	/*
	 * 验证用户名是否符合规则
	 * @param	$user	需要验证的用户名
	 * @param	$min	最少长度
	 * @param	$max	最大长度	
	 */
	public function checkUser($user,$min=6,$max=30)
	{
		$reg = '/^[a-zA-Z]\w{'.($min-1).','.($max-1).'}$/';
		preg_match($reg, $user,$match);
		//php中空数组会自动转换成false
		if(!$match){
			//不符合规则
			$this -> error[] = 
			'<font color="red">'.$min.'-'.$max.'位字母、数字、或下划线，字母开头</font>';
			return false;
		}else{
			//符合规则
			return true;
		}
	}
	/*
	 * 验证密码是否符合规则
	 * @param	$pass	待验证的密码
	 * @param	$min	最少多少位
	 * @param	$max	最多多少位
	 */
	public function checkPass($password,$min=6,$max=20)
	{
		
		$reg1 = '/^[a-zA-Z]{6,20}$/';
		$reg2 = '/^[0-9]{6,20}$/';
		$reg3 = '/^[a-zA-Z0-9]{6,20}$/';		//安全性高一级
		$reg4 = '/^[a-zA-Z0-9~!@#$%\^&\*\(\)-_\+=\[\]\{\}\|\;:\'"<>,\.\?\/]{6,20}$/';

		preg_match($reg1, $password,$match1);
		preg_match($reg2, $password,$match2);
		preg_match($reg3, $password,$match3);
		preg_match($reg4, $password,$match4);
		
		if($match1 || $match2){
			//通过了说明是字母或纯数字的
			echo '纯字母、数字的密码，强度一般<br>';
			return true;
		}elseif($match3){
			//说明是字母、数字组合的时候
			echo '字母数字的组合，强度中等<br>';
			return true;
		}elseif($match4){
			//说明字母、数字、特殊符合组合
			echo '字母数字特殊符合的组合，强度杠杠的<br>';
			return true;
		}else{
			$this -> error[] = '<font color="red">密码不符合规则</font>';
			return false;
		}	
		
	}
	/*
	 * 验证邮箱是否符合规则
	 * @param	$email	待验证的邮箱
	 */
	public function checkEmail($email)
	{
		$reg = '/^[\w\.\-]+@[a-zA-Z\d]+(\.[a-zA-Z\d]+)?\.[a-zA-Z]{2,4}$/';
		preg_match($reg, $email,$res);
		if(!$res){
			//说明不符合规则
			$this -> error[] =
			'<font color="red">邮箱地址不符合规则</font>';
			return false;
		}else{
			//符合规则
			return true;
		}
	}
	/*
	 * 验证手机号码是否符合规则
	 * @param	$phoneNum	手机号码
	 */
	public function checkPhone($phoneNum)
	{
		$reg = '/^1[34578]\d{9}$/';		//使用^ $表示从开始到结束共11位，更精确
		preg_match($reg, $phoneNum,$match);
		if(!$match){
			//说明数组为空,不符合规则
			$this -> error[] =
			'<font color="red">手机格式有误</font>';
			return false;
		}else{
			//符合规则
			return true;
		}
	}
	
}