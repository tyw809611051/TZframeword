<?php 
namespace admin\controller;
use framework\core\Controller;
use framework\core\Factory;
class DiscussController extends Controller {

	// 展示话题/讨论数据页
	public function indexAction() {
		// 实例化话题模型
		$dis_model = Factory::M('Discuss');

		$dis_list = $dis_model -> getAllDis();

		// 分配变量与视图
		$this->_smarty->assign('dis_list',$dis_list);
		$this->_smarty->display('discuss/index.html');
	}

	// 删除话题
	public function deleteAction() {
		// 获取删除id
		$dis_id = $_GET['id'];
		// 校验数据是否还有子话题
		$dis_model = Factory::M('Discuss');
		$res = $dis_model -> checkSubData($dis_id);

		if($res != false) {
			$result = $dis_model -> delete($dis_id);
			if($result !=false) {
				$this -> jumpURL('删除成功',Factory::U('admin/Discuss/index'));
			} else {
				$this -> jumpURL('删除成功',Factory::U('admin/Discuss/index'));
			}
		} else {
			$this ->jumpURL('删除失败,原因如下：<br>'.$dis_model->showErr(),Factory::U('admin/Discuss/index'));
		}
	}

}


 ?>