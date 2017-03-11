<?php 
namespace home\model;
use framework\core\Model;

class UserModel extends Model {

	public $_logic_table = 'user';

		//查询热门用户
	public function getHotUsers()
	{
		$sql = "SELECT u.username,u.user_pic, count(q.question_id) AS q_num 
				FROM ask_user AS u LEFT JOIN ask_question AS q 
				ON u.user_id=q.user_id GROUP BY q.user_id ORDER BY q_num DESC LIMIT 3";
		return $this -> _dao -> fetchAll($sql);		
	}
	//查询用户名、邮箱是否已经注册
	public function isExists($user,$email)
	{
		//查询：只要用户名、或 邮箱有任何一个已经注册了，就不能再注册
		$sql = "SELECT 1 FROM $this->_true_table WHERE username='$user' or email='$email'";
		return $this -> _dao -> fetchColumn($sql);
	}
	//验证激活码是否有效
	public function checkActiveCode($username,$active_code)
	{
		$sql = "SELECT reg_time,is_active FROM $this->_true_table WHERE username='$username' 
		and active_code = '$active_code'";
		return $this -> _dao -> fetchRow($sql);
	}
	//验证用户名密码是否正确
	public function checkUser($username,$password)
	{
		//为了防止sql注入，对用户名中的引号进行转义、包裹操作
		$user = $this -> _dao -> quoteValue($username);		
		$sql = "SELECT * FROM $this->_true_table WHERE username=$user and 
		password='$password'";
		
		return $this -> _dao -> fetchRow($sql);
	}
	//验证用户名、手机是否被注册
	public function checkUserByPhone($user,$phoneNum)
	{
		//查询：只要用户名、或 邮箱有任何一个已经注册了，就不能再注册
		$sql = "SELECT 1 FROM $this->_true_table WHERE username='$user' or phone_nums='$phoneNum'";
		return $this -> _dao -> fetchColumn($sql);
	}
	
}



 ?>