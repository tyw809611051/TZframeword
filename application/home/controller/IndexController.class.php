<?php 
namespace home\controller;
use framework\core\Controller;
use framework\core\Factory;

class IndexController extends Controller {

	// 展示首页
	public function indexAction() {
		// 显示分类
		$cate_model = Factory::M('Category');
		$cate_name = $cate_model ->getCatName();
		//显示推荐分类（暂未做推荐权重）
		$cate_recom = $cate_model ->getRecomCat();

		// 显示最新文章
		$art_model = Factory::M('Article');
		$art_list = $art_model -> getHotArt();
		
		// 分配变量与视图
		$this ->_smarty->assign('cate_name',$cate_name);
		$this ->_smarty->assign('art_list',$art_list);
		$this ->_smarty->assign('cate_recom',$cate_recom);
		
		$this->_smarty->display('index.html');
	}
}






 ?>