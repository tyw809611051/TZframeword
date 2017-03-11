<?php 
namespace admin\model;
use framework\core\Model;
class CategoryModel extends Model {
	public $_logic_table = 'category';

	// 获取分类数据
	public function getAllCat() {

		$sql = "SELECT cat_id,cat_name,cat_pic,cat_desc From `$this->_true_table`";

		return $this->_dao->fetchAll($sql);
	}

	// 校验数据是否为空
	public function isEmpty($cat_name,$cat_desc) {

		if($cat_name == '') {
			$this->_error[] = '分类标题不能为空';

		} else if($cat_desc == '') {
			$this->_error[] = '分类描述不能为空';
		}

		if(!empty($this->_error)) {
			return false;

		} else {

			return true;
		}
	}

	// 获取所有分类名信息
	public function getCatName() {
		
		$sql = "SELECT cat_id,cat_name From `$this->_true_table`";

		return $this->_dao->fetchAll($sql);
	}



}





 ?>