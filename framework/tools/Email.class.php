<?php
namespace framework\tools;
use framework\vendor\PHPMailer\PHPMailer;
class Email
{
	//定义静态方法发送邮件
	//邮件内容、发送者邮箱这些内容我们以参数的形式传递进来
	//参数1：接收者的邮箱地址
	//参数2：
	public static function send($to,$title,$content)
	{
		//实例化对象
		$mail = new PHPMailer();
		
		//3.设置属性，告诉我们的服务器，谁跟谁发送邮件
		$mail -> IsSMTP();				//告诉服务器使用smtp协议发送
		$mail -> SMTPAuth = true;		//开启SMTP授权
		$mail -> Host = '';	//告诉我们的服务器使用163的smtp服务器发送
		//如果使用的是qq的或者新浪smtp服务器smtp.sina.cn
		/******************下面的改为自己的邮箱地址************************************/
		$mail -> From = '';	//发送者的邮件地址
		$mail -> FromName = '';			//发送邮件的用户昵称
		$mail -> Username = '';		//登录到邮箱的用户名
		$mail -> Password = '';	//第三方登录的授权码，在邮箱里面设置
		
		//编辑发送的邮件内容(这里保持默认值)
		$mail -> IsHTML(true);			//发送的内容使用html编写
		$mail -> CharSet = 'utf-8';		//设置发送内容的编码
		
		//发送的内容，可以修改
		$mail -> Subject = $title;		//设置邮件的主题、标题
		$mail -> MsgHTML($content);			//发送的邮件内容主体
		//告诉服务器接收人的邮件地址
		$mail -> AddAddress($to);
		//调用send方法，执行发送
		$result = $mail -> Send();
		
		if($result){
			echo '发送成功';
		}else{
			echo $mail -> ErrorInfo;
		}
	}
}