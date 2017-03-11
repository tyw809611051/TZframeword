<?php 
namespace admin\controller;
use framework\core\Controller;
use framework\core\Factory;
use framework\tools\Upload;
use framework\tools\Thumb;
class CategoryController extends Controller {

	// 展示后台分类首页
	public function indexAction() {
		// 展示所有数据
		$cate_model = Factory::M('Category');

		$cate_list = $cate_model -> getAllCat();

		// 分配变量与视图
		$this->_smarty->assign('cate_list',$cate_list);
		$this->_smarty->display('category/index.html');
	}

	// 跳转添加页面
	public function addAction() {

		$this->_smarty->display('category/add.html');
	}

	// 处理添加数据
	public function addHandleAction() {

		// 接收数据
		$data = $_POST;
	
		// 验证数据是否为空，为空退出
		$cate_model = Factory::M('Category');

		$res = $cate_model -> isEmpty($data['cat_name'],$data['cat_desc']);

		if($res != false) {
			/*******上传文件*********/
			$upload = new Upload();
			// 设置上传路径
			$date = date('Ymd');
			$upload ->setUploadPath(UPLOAD_PATH.'category/');
			// 返回文件路径
			$filename = $upload ->doUpload($_FILES['cat_pic']);	

			/*********压缩文件*************/ 
			$thumb = new Thumb($filename);
			$thumb -> setThumbPath(THUMB_PATH.'category/');
			$file_path = $thumb ->makeThumb(100,100);

			// 添加至数据库
			$data['cat_pic'] = $file_path;
			$result = $cate_model -> insert($data);

			if($result != false) {
				$this->jumpURL('添加成功',Factory::U('admin/Category/index'));
			} else {

				$this->jumpURL('添加失败',Factory::U('admin/Category/add'));
			}
		} else {
			// 数据为空
			$this->jumpURL('添加失败，原因如下：<br>'.$cate_model->showErr(),Factory::U('admin/Category/add'));
		}
	}

	// 删除数据
	public function deleteAction() {
		
		$cat_id = $_GET['id'];

		// 删除图片,先查询出图片信息
		$cate_model = Factory::M('Category');

		$data = array('cat_pic');
		$where = array('cat_id'=>$cat_id);
		// 查询出图片路径
		$file_path = $cate_model -> selectRow($data,$where);
		// 删除压缩图
		@unlink(THUMB_PATH.'category/'.$file_path['cat_pic']);
		// 删除原图
		$file = str_replace('thumb_', '',$file_path['cat_pic']);
		@unlink(UPLOAD_PATH.'category/'.$file);
		
		// 删除数据
		$res = $cate_model -> delete($cat_id);

		if($res != false) {
			$this ->jumpURL('删除成功',Factory::U('admin/Category/index'));
		} else {
			$this ->jumpURL('删除失败',Factory::U('admin/Category/index'));
		}
	}
	// 展示编辑页面
	public function editAction() {
		// 接收编辑id
		$cat_id = $_GET['id'];
		// 查询出编辑的数据
		$cate_model = Factory::M('Category');

		$where = array('cat_id'=>$cat_id);
		// 查询出图片路径
		$cate_row = $cate_model -> selectRow(null,$where);

		//分配变量与视图
		$this ->_smarty ->assign('cate_row',$cate_row);
		$this ->_smarty ->display('category/edit.html');
	}

	// 处理编辑数据
	public function updateAction() {

		$data['cat_name'] = $_POST['cat_name'];
		$data['cat_desc'] = $_POST['cat_desc'];
		// 判断数据是否为空
		$cate_model = Factory::M('Category');
		$res = $cate_model -> isEmpty($data['cat_name'],$data['cat_desc']);

		// 判断是否上传图片
		if($res != false ) {
			// 更新数据
			if($_FILES['cat_pic']['error'] === 0) {
				/*******上传文件*********/
				$upload = new Upload();
				// 设置上传路径
				$date = date('Ymd');
				$upload ->setUploadPath(UPLOAD_PATH.'category/');
				// 返回文件路径
				$filename = $upload ->doUpload($_FILES['cat_pic']);	

				/*********压缩文件*************/ 
				$thumb = new Thumb($filename);
				$thumb -> setThumbPath(THUMB_PATH.'category/');
				$file_path = $thumb ->makeThumb(100,100);

				// 删除压缩图与原图
				// 删除压缩图
				@unlink(THUMB_PATH.'category/'.$_POST['old_cat_pic']);
				// 删除原图
				$file = str_replace('thumb_', '',$_POST['old_cat_pic']);
				@unlink(UPLOAD_PATH.'category/'.$file);
				// 更新路径
				$data['cat_pic'] = $file_path;
			}

			// 更新数据
			$where = array('cat_id'=>$_POST['cat_id']);
			$result = $cate_model -> update($data,$where);

			// 判断是否更新成功
			if($result != false) {
				$this -> jumpURL('更新成功',Factory::U('admin/Category/index'));
			} else {
				// 更新数据库失败
				$this -> jumpURL('更新失败',Factory::U('admin/Category/edit',['id'=>$_POST['cat_id']]));
			}
		} else {
			// 为空
			$this -> jumpURL('更新失败,原因如下：<br>'.$cate_model->showErr(),Factory::U('admin/Category/edit',['id'=>$_POST['cat_id']]));
		}
		
	}
}





 ?>