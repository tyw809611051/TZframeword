<?php 
namespace admin\controller;
use framework\core\Controller;
use framework\core\Factory;
class ArticleController extends Controller {

	// 展示文章数据页
	public function indexAction() {

		// 获取文章、分类数据
		$art_model = Factory::M('Article');

		$art_list = $art_model -> getAllArt();

		// 分配变量和视图
		$this->_smarty->assign('art_list',$art_list);
		$this->_smarty->display('article/index.html');
	}

	// 展示新增文章页
	public function addAction() {
		//获取所有标题目录信息
		$art_model = Factory::M('Article');
		$art_list = $art_model -> getAllDir();
		// 获取树状输出
		$art_tree = $art_model -> getTreeData($art_list);

		// 获取所有文章分类信息
		$cate_model = Factory::M('Category');

		$cate_name = $cate_model -> getCatName();

		$this ->_smarty->assign('art_tree',$art_tree);
		$this ->_smarty->assign('cate_name',$cate_name);
		$this ->_smarty->display('article/add.html');
	}

	// 处理添加数据
	public function addHandleAction() {
		
		if(isset($_POST['editorValue']) && $_POST['art_title'] != '') {
				// 验证数据是否为空
			$art_model = Factory::M('Article');

			$data['art_title'] = $_POST['art_title'];
			$data['art_content'] = $_POST['editorValue'];
			$data['parent_id'] = $_POST['parent_id'];
			$data['cat_id'] = $_POST['cat_id'];
			$data['pub_time'] = time();
			// 添加数据
			$result = $art_model -> insert($data);

			if($result != false) {
				$this -> jumpURL('新增成功',Factory::U('admin/Article/index'));
			} else {
				// 插入数据失败
				$this -> jumpURL('新增失败',Factory::U('admin/Article/add'));
			}
			
		} else {
			$this ->jumpURL('文章或标题可不能为空',Factory::U('admin/Article/add'));
		}
		
	}

	// 处理删除数据
	public function deleteAction() {
		// 接收删除的id
		$art_id = $_GET['id'];

		$art_model = Factory::M('Article');
		// 判断该文章下面是否还有子目录
		$res = $art_model->checkSubData($art_id);
		
		if($res == false) {
			$result = $art_model -> delete($art_id);

			if($result != false) {
				$this -> jumpURL('删除成功',Factory::U('admin/Article/index'));
			} else {
				$this -> jumpURL('删除失败',Factory::U('admin/Article/index'));
			}
		} else {
			// 有子目录
			$this -> jumpURL('删除失败，原因如下：<br>'.$art_model->showErr(),Factory::U('admin/Article/index'));
		}
	}

	// 展示编辑页面
	public function editAction() {
		// 接收id值，收集该id值的数据
		$art_id = $_GET['id'];
		$art_model = Factory::M('Article');

		$data = array('art_title','art_content','parent_id','cat_id');
		$where = array('art_id'=>$art_id);
		$art_row = $art_model -> selectRow($data,$where);

		// 获取所有数据
		$art_list = $art_model -> getAllDir();
		// 获取树状输出
		$art_tree = $art_model -> getTreeData($art_list);

		// 获取所有文章分类信息
		$cate_model = Factory::M('Category');
		$cate_name = $cate_model -> getCatName();

		$this ->_smarty->assign('art_tree',$art_tree);
		$this ->_smarty->assign('cate_name',$cate_name);
		
		$this ->_smarty ->assign('art_row',$art_row);
		// 跳转编辑页面
		$this ->_smarty ->display('article/edit.html');
	}

	// 处理更新数据
	public function updateAction() {
		
	}
}


 ?>
