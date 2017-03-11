<?php 
namespace home\model;
use framework\core\Model;
class CategoryModel extends Model {
	public $_logic_table = 'category';

	// 获取分类数据
	public function getAllCat() {

		$sql = "SELECT cat_id,cat_name,cat_pic,cat_desc From `$this->_true_table`";

		return $this->_dao->fetchAll($sql);
	}

	// 获取所有分类名信息
	public function getCatName() {
		
		$sql = "SELECT cat_id,cat_name,cat_desc From `$this->_true_table`";

		return $this->_dao->fetchAll($sql);
	}

	// 获取推荐分类数据
		public function getRecomCat() {

		$sql = "SELECT cat_id,cat_name,cat_pic,cat_desc From `$this->_true_table` LIMIT 0,3";

		return $this->_dao->fetchAll($sql);
	}



}





 ?>