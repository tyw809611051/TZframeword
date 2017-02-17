<?php
/*
 * 基础控制器类
 * 封装各个控制器公共的方法
 */
namespace framework\core;
use Smarty;
class Controller
{
    protected $smarty;
    public function __construct()
    {
        //初始化smarty
        $this -> initSmarty();
        //初始化session_start()
        $this -> initSession();        
    }
    private function initSmarty()
    {
        $this->smarty = new Smarty();
        //设置模板目录
        $this->smarty->setTemplateDir(APP_PATH.MODULE.'/view/');
        //设置编译文件目录
        $this->smarty->setCompileDir(APP_PATH.'runtime/tpls_c/');
        //设置模板变量的定界符{}  ---->  <{}>
        $this->smarty->left_delimiter = '<{';
        $this->smarty->right_delimiter = '}>';
    }
    private function initSession()
    {
    	session_start();
    }
	//跳转操作
	public function jump($message,$url,$delay=3)
	{
		echo $message;
		header("Refresh:$delay;url=$url");
		die;
	}
	//验证用户是否合法（防跳墙操作）
	public function isLogin()
	{
		//1. 先判断是否有session，因为登录成功时将用户的信息保存到session中
		if(!isset($_SESSION['user'])){
			//如果没有设置session（说明用户没有登录）
			//2. 可能上次登录成功时把信息保存到cookie
			if(isset($_COOKIE['uname'])){
				//3. 还要判断7天之内是否修改过密码
				$m_user = Factory::M(MODULE.'\\model\\User');
				$res = $m_user -> checkUser($_COOKIE['uname'],$_COOKIE['pwd']);
				if(!$res){
					//说明密码已过期,重新登录
					$this -> jump('密码已过期，请重新登录','?c=user&a=loginAction');
				}else{
					$_SESSION['user'] = $res;
				}
			}else{
				//既没有session又没有cookie
				$this -> jump('请先登录，凑什么热闹','?c=user&a=loginAction');
			}
		}
	}
}