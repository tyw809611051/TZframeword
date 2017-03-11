<?php 
namespace home\controller;
use framework\core\Controller;
use framework\core\Factory;

class ArticleController extends Controller {

	// 接收分类id值，展示该目录下面所有的目录
	public function indexAction() {
		// 显示分类标题
		$cate_model = Factory::M('Category');
		$cate_name = $cate_model ->getCatName();
		// 接收id值，展示该id下面所有目录
		$cat_id = $_GET['id'];
		$art_model = Factory::M('Article');
		$art_list = $art_model -> getIDArt($cat_id);
		// 分配变量与视图
		$this->_smarty->assign('cate_name',$cate_name);
		$this ->_smarty ->assign('art_list',$art_list);;
		$this->_smarty->display('category/cate.html');
	}

	// 展示每个标题下面详细的文章
	public function detailAction() {
		// 显示分类
		$cate_model = Factory::M('Category');
		$cate_name = $cate_model ->getCatName();
		// 接收id值,得到目录
		$cat_id = $_GET['id'];
		$art_model = Factory::M('Article');
		$art_list = $art_model -> getIDArt($cat_id);
		// 得出文章
		$art_id = $_GET['art_id'];
		$art_content = $art_model -> getDetailContent($art_id);
		
		// 分配变量与视图
		$this->_smarty->assign('cate_name',$cate_name);
		$this ->_smarty ->assign('art_list',$art_list);
		$this->_smarty->assign('art_content',$art_content);
		$this->_smarty->display('category/detail.html');
	}
}








 ?>