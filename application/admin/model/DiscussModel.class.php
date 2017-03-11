<?php 
namespace admin\model;
use framework\core\Model;

class DiscussModel extends Model {

	public $_logic_table = 'discuss';

	// 获取所有话题数据
    public function getAllDis() {

    	$sql = "SELECT u.user_id,u.username,d.dis_id,d.dis_title,d.dis_mtime,a.art_id,a.art_title
    			FROM bg_user AS u,bg_discuss AS d,bg_article as a
    			WHERE u.user_id=d.user_id and a.art_id=d.art_id";

    	return $this->_dao->fetchAll($sql);
    }
}




 ?>