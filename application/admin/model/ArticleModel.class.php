<?php 
namespace admin\model;
use framework\core\Model;

class ArticleModel extends Model {
	public $_logic_table = 'article';

	// 获取文章、分类数据
	public function getAllArt() {

		$sql = "SELECT c.cat_id,c.cat_name,a.art_id,a.art_title,a.parent_id,a.pub_time FROM bg_article AS a,bg_category AS c WHERE c.cat_id=a.cat_id";

		return $this->_dao->fetchAll($sql);
	}

	// 获取所有目录信息
	public function getAllDir() {

		$sql = "SELECT art_id,art_title,parent_id,cat_id FROM `$this->_true_table`";

		return $this ->_dao ->fetchAll($sql);
	}

	// 获取树状目录
	public function getTreeData($list,$art_id=0,$leven=0) {
		// 保存分好类数组
		static $arr = array();
		foreach($list as $v) {

			if($v['parent_id'] == $art_id) {

				$v['leven'] = $leven;
				$arr[] = $v;

				$this ->getTreeData($list,$v['art_id'],$leven+1);
			}
		}
		return $arr;		
	}

// 校验数据是否为空
	public function isEmpty($cat_name,$cat_desc) {

		if($cat_name == '') {
			$this->_error[] = '文章标题不能为空';

		} else if($cat_desc == '') {
			$this->_error[] = '文章描述不能为空';
		}

		if(!empty($this->_error)) {
			return false;

		} else {

			return true;
		}
	}

	// 校验该目录下是否还有子目录
	public function checkSubData($art_id) {

		$sql = "SELECT 1 FROM `$this->_true_table` WHERE parent_id=$art_id";

		$res = $this ->_dao ->fetchColumn($sql);

		if($res) {
			// 有子目录
			$this->_error[] = '该目录下还有子目录,不能删除';
			return true;
		} else {
			return false;
		}
	}
}



 ?>