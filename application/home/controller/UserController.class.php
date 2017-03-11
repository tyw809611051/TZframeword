<?php 
namespace home\controller;
use framework\core\Controller;
use framework\tools\Captcha;
use framework\tools\Verify;
use framework\core\Factory;
use framework\tools\Email;
use framework\tools\Message;
class UserController extends Controller {

	// 展示验证码
	public function showCaptchaAction()
	{
		$captcha = new Captcha();
		$captcha -> _height = 40;
		$captcha -> makeImage();
	}
	// 展示邮箱注册页面
	public function registerEmailAction() {

		$this ->_smarty ->display('user/register.html');
	}

	// 展示手机验证码注册页面
	public function sendMessageAction() {

		// 展示手机验证码注册页面
		$this ->_smarty->display('user/msm_register.html');
	}

	// 展示注册页面
	public function registerAction() {
		// 展示注册页面
		$this->_smarty ->display('user/do_register.html');
	}

	// 展示登录页面
	public function loginAction() {
		// 展示登录页面
		$this->_smarty ->display('user/login.html');
	}

	// 验证邮箱注册提交的数据
	public function addHandleAction() {
		
		// 判断是否同意用户协议
		if(isset($_POST['agreement_chk'])) {
			// 判断验证码是否正确
			$captcha = new Captcha();
			$res = $captcha ->checkCode($_POST['seccode_verify']);
			if($res != false) {
				// 判断用户名、邮箱规则是否正确
				$verify = new Verify();
				$res1 = $verify -> checkUser($_POST['user_name']);
				$res2 = $verify -> checkPass($_POST['password']);
				$res3 = $verify -> checkEmail($_POST['email']);
				if($res1 && $res2 && $res3) {
					// 判断用户名、邮箱是否已经存在
					$user_model = Factory::M('User');

					$username = $_POST['user_name'];
					$email = $_POST['email'];
					// 验证是否存在相同用户名，返回0/1
					$res = $user_model -> isExists($username,$email);

					if(!$res) {	#不存在相同用户名

						// 组合数据库信息，验证信息
						$data['username'] = $username;
						$data['password'] = md5($_POST['password']);	#md5加密
						$data['email'] 	  = $email;
						$data['reg_time'] = time();
						$data['is_active'] = 0;

						// 组合邮箱验证码
						$active_code = md5(mt_rand(1000,9000).time());
						$data['active_code'] = $active_code;

						// 添加进数据库,再发送邮箱
						$res = $user_model -> insert($data);
						if($res) {
							// 添加成功，发送邮箱
							$title = "恭喜您，注册成功，欢迎！";	#邮箱标题
							$content = <<<CONTENT
							<p>请点击进行激活</p>
							<a href="http://localhost/PHP05/12-26message/1223/index.php?m=home&c=User&a=isActive&user={$username}&activeCode={$active_code}">http://localhost/PHP05/12-26message/1223/index.php?m=home&c=User&a=isActive&user={$username}&activeCode={$active_code}</a>
							<p>如果点击无效，请复制到地址栏打开;1天内有效</p>
CONTENT;


							// 发送邮件
							Email::send($email,$title,$content);
							$this -> jumpURL('<br>注册成功，激活邮箱发送中...','?m=home&c=User&a=login');
						} else {
							// 添加失败、注册失败
							$this->jumpURL('<br>注册失败','?m=home&c=User&a=registerEmail');

						}
					} else {
						// 返回1，代表存在相同用户名和密码
						$this ->jumpURL('<br>用户名或邮箱已经存在','?m=home&c=User&a=registerEmail');
					}
					
				} else {
					// 用户名、邮箱不符合规则
					$this -> jumpURL('<br>注册失败，原因如下:<br>'.$verify->showError(),'?m=home&c=User&a=registerEmail');
				}
				
			} else {
				// 验证码错误
				$this ->jumpURL('<br>验证码错误，请重试','?m=home&c=User&a=registerEmail');
			}
			
		} else {
			// 未同意用户协议
			$this -> jumpURL('请先同意用户协议','?m=home&c=User&a=registerEmail');
		}

	}

	// 处理手机验证码提交数据
	public function sendHandleAction() {
		// 是否同意协议，不同意直接退出
		if(isset($_POST['agreement_chk'])) {
			// 验证码是否正确，不正确直接退出
			$captcha = new Captcha();
			$result = $captcha -> checkCode($_POST['seccode_verify']);
			if($result) {
				
				// 验证手机号码是否符合规则
				$verify = new Verify();
				$res = $verify ->checkPhone($_POST['phone']);
				// 验证手机号码是否已经存在
				if($res) {
					#符合规则
					// 实例化短信类，验证发送短信是否成功
					$message = new Message();
					$code = mt_rand(1000,9999);	#随机验证码
					// 短信配置
					$expire_time = $GLOBALS['config']['expire_time'];	#时间
					$tempId = $GLOBALS['config']['tempID'];	#应用id
					$datas = array($code,$expire_time);
					// 发送短信
					$result = $message ->sendTemplateSMS($_POST['phone'],$datas,$tempId);
					if($result['status']) {
						// 说明发送成功
						// 保存时间，验证码，手机号码
						$data['message_code'] = $code;
						$data['send_time'] = time();
						$data['phone_num'] = $_POST['phone'];

						// 实例化短信验证码模型类
						$message_model = Factory::M('Message');
						$res = $message_model ->insert($data);

						if($res != false) {
								// 添加成功
							$this->jumpURL('发送成功...请验证','?m=home&c=User&a=register');
						} 

					} else {

							// 发送失败
							$this->jumpURL('发送失败，请重试','?m=home&c=User&a=sendMessage');
						}
					

				} else {

					$this -> jumpURL('手机格式不正确','?m=home&c=User&a=sendMessage');
				}
				
			} else {

				$this ->jumpURL('验证码错误，请重试','?m=home&c=User&a=sendMessage');
			}
			
		} else {

			$this-> jumpURL('请先同意用户协议','?m=home&c=User&a=sendMessage');
		}

	}

	// 处理手机注册页面提交数据
	public function doRegisterAction() {
		
		// 验证是否同意协议
		if(isset($_POST['agreement_chk'])) {

			// 验证码是否正确，不正确直接退出 
			$captcha = new Captcha();
			$res = $captcha -> checkCode($_POST['seccode_verify']);
			//验证码结果处理
			if($res != false) {

				// 验证通过,收集用户数据
				$username = $_POST['user_name'];
				$password = $_POST['password'];	
				$phone = $_POST['msm'];
				$message_code = $_POST['msm_code'];
				// 验证用户名，邮箱、手机号码是否符合规则
				$verify = new Verify();
				$res1 = $verify ->checkUser($username);
				$res2 = $verify ->checkPass($password);
				$res3 = $verify ->checkPhone($phone);

				if($res1 && $res2 && $res3) {

					// 验证用户名、手机号码是否已经存在
					$user_model = Factory::M('User');
					$result = $user_model -> checkUserByPhone($username,$phone);

					if(!$result) {	
						$message = Factory::M('Message');
						// 验证短信验证码和手机号码是否存在且正确
						$res = $message -> checkMessage($phone,$message_code);
						
						if($res) {
							// 验证码存在
							if(time() - $res['send_time'] < 24*3600) {
								// 验证短信验证是否过期							
								// 设置属性，保存用户名，密码，注册时间，激活码、激活为0
								$data['username'] = $username;
								$data['password'] = md5($password);	#加密密码
								$data['phone_nums'] = $phone;
								$data['reg_time'] = time();
								$data['is_active'] = 1;	#没有激活
								
								$res = $user_model ->insert($data);
								if($res != false) {
									$this->jumpURL('注册成功','?m=home&c=User&a=login');
								}
							} else {
								// 验证码过期了
								$this ->jumpURL('验证码过期了','?m=home&c=User&a=register');
							}
						
						} else {
							// 验证码不存在
							$this -> jumpURL('短信验证码错误','?m=home&c=User&a=register');
						}
						
					}  else {
							// 存在相同用户名或手机
						$this -> jumpURL('存在相同用户名或手机号码','?m=home&c=User&a=register');
					}

					
				} else {
					// 数据规则不正确
					$this ->jumpURL('注册失败，原因如下:'.$verify->showError(),'?m=home&c=User&a=register');
				}
			
			}  else {
				// 验证码不通过
				$this ->jumpURL('验证码错误','?m=home&c=User&a=register');
			}
			
		} else {

			$this -> jumpURL('请先同意用户协议','?m=home&c=User&a=register');
		}

	}

	// 处理登录页面提交数据
	public function loginHandleAction() {
		
		// 验证用户名，密码是否正确
		$username = $_POST['username'];
		$password = md5($_POST['password']);

		$user_model = Factory::M('User');
		$res = $user_model -> checkUser($username,$password);	#返回所有数据

		if($res != false) {
			// 验证是否激活
			if($res['is_active'] == 1) {
				
				if(isset($_POST['net_auto_login'])) {
					// 激活了，下次直接登录，并保存用户名，到session和cookie
					setcookie('uname',$username,time()+7*24*3600,'/');
					setcookie('pwd',$password,time()+7*24*3600,'/');
					$_SESSION['user'] = $username;
						
					$this ->jumpURL('登录成功','?m=home&c=Index&a=index');

				} else {
					// 激活了，下次不直接登录，保存用户信息到session和cookie中
					$_SESSION['user'] = $username;
					//var_dump($_SESSION['user']);die;
					$this ->jumpURL('登录成功','?m=home&c=Index&a=index');
				}
				
			} else {

				// 用户未激活
				$this -> jumpURL('还未激活，请激活','?m=home&c=User&a=login');
			}
			
		} else {
			// 用户名或密码不存在
			$this -> jumpURL('用户名或密码不存在','?m=home&c=User&a=login');
		}

	}

	// 处理邮件激活操作
	public function isActiveAction() {

		// 接收地址栏的激活码和用户名
		$username = $_GET['user'];
		$active_code = $_GET['activeCode'];
		// 验证激活码是否有效
		$user_model = Factory::M('User');
		$res = $user_model ->checkActiveCode($username,$active_code);	#返回注册时间、激活状态
		if($res != false) {
			// 判断激活码是否过期
			if(time() - $res['reg_time'] < 24*3600) {
				// 判断是否激活过了
				if($res['is_active'] != 1) {
					// 没有激活，则更新激活字段
					// 判断是否更新成功
					$data['is_active'] = 1;
					$where['username'] = $username; 
					$res = $user_model ->update($data,$where);

					if($res != false) {
						// 状态已更新，激活成功
						$this -> jumpURL('激活成功','?m=home&c=User&a=login');
					} else {
						// 激活失败
						$this ->jumpURL('激活失败','?m=home&c=User&a=register');
					}
				
				} else {
				
					// 已经激活过了
					$this ->jumpURL('邮箱已激活过了，请不要重复操作','?m=home&c=User&a=login');
				}
				
			} else {
				// 激活码过期了
				$this ->jumpURL('激活码过期了','?m=home&c=User&a=register');
			}
			
		} else {
			// 激活码无效
			$this ->jumpURL('激活码无效','?m=home&c=User&a=register');

		}
		
	}

}








 ?>