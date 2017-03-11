<?php 
namespace admin\controller;
use framework\core\Controller;
use framework\core\Factory;
use framework\tools\HttpRequest;

class MessageController extends Controller {

	// 展示收集页面
	public function addAction() {

		$this->_smarty->display('message/add.html');
	}

	public function addHandleAction() {

		$url = $_POST['url'];
		//echo $url;
		//开始使用curl这个工具，请求url中的资源
		$http = new HttpRequest();
		$result = $http -> send($url);
		if($result['status']) {
			$reg = '/<a[^>]+href="\/wiki\/001434446689867b27157e896e74d51a89c25cc8b43bdb3000\/001450409116077b5e000f9cb57448785b181b0939caecd000">(.+?)<\/a>.+?<div[^>]+class="x-wiki-content">(.+?)<\/div>/su';
			preg_match($reg, $result['msg'],$matchs);

			// 存进标题表中
			$data['art_title'] = $matchs[1];
			$data['art_content'] = $matchs[2];
			$data['parent_id'] = 61;
			$data['pub_time'] = time();
			$data['view_nums'] = 1;
			$data['reply_nums'] = 1;
			$data['cat_id'] = 7;

			$art_model = Factory::M('Article');
			$res = $art_model -> insert($data);
			if($res !=false) {
				$this->jumpURL('添加成功',Factory::U('admin/Message/add'));
			} else {
				$this->jumpURL('添加失败',Factory::U('admin/Message/add'));
			}
			
		} else {
			$this->jumpURL('采集失败'.$result['msg'],Factory::U('admin/Message/add'));
		}
		
	}
}





 ?>