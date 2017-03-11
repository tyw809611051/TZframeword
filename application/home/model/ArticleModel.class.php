<?php 
namespace home\model;
use framework\core\Model;

class ArticleModel extends Model {
	public $_logic_table = 'article';

	// 按时间排序提取文章
	public function getHotArt() {

		$sql = "SELECT art_id,art_title,cat_id FROM `$this->_true_table` ORDER BY pub_time DESC LIMIT 0,10";

		return $this->_dao ->fetchAll($sql);
	}

	// 取出指定id下面的所有的文章信息与回复用户信息
	public function getIDArt($id) {

		// 取出目录和文章
		$sql = "SELECT art_id,art_title,art_content,parent_id,pub_time,view_nums,cat_id FROM `bg_article` WHERE cat_id = $id";

		return $this->_dao->fetchAll($sql);
	}

	// 取出评论
	public function getIDDis($art_id) {

		$sql = "SELECT dis.dis_id,dis.dis_title,dis.dis_content,dis.dis_ctime,dis.dis_mtime,dis.parent_id,dis.is_verify,u.user_id,u.username,u.user_pic
				FROM bg_discuss as dis,bg_user as u
				WHERE u.user_id=dis.dis_id and dis.art_id = $art_id";

		return $this ->_dao->fetchAll($sql);
	}

	// 取出文章
	public function getDetailContent($art_id) {
		$sql = "SELECT art_id,art_content FROM bg_article WHERE art_id=$art_id";

		return $this->_dao->fetchRow($sql);
	}


}







 ?>