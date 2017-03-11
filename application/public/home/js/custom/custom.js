// 对首页的图片进行轮播
function HotContent() {
	// 获取ul元素
	var oUl = document.getElementsByClassName('uk-overlay-active')[0];
	var oli = oUl.getElementsByTagName('li');
	console.log(oli);
	var count = 1;
	// 设计定时器，自动播放
			setInterval(function(){
				for(var i=0;i<oli.length;i++) {
					oli[i].className = "";
				}
				oli[count-1].className = "uk-active";
				count++;
				if(count>5) {
					count= 1;
				}
			}, 10000);	
}

// 回滚动至顶部事件
function scrollTop() {
	// 获取顶部元素
         var oGoTop = document.getElementsByClassName('x-goto-top')[0];
        
        oGoTop.onclick = function() {

            // 滚动事件为0
           document.body.scrollTop = 0;
        }
}

/**
 * 〈编辑提交之后的提示处理〉
 * @param [参数1]     [参数1说明]
 * @param [参数2]     [参数2说明]
 * @return[返回类型说明]
 */
 // 学了文件再做
 function updateJS() {
 	// 获取提交的数据(input的数值)，并传到指定方法中进行验证
 	var oInput = document.getElementsByTagName('input');
 	var data = [];
 	// 获取id值
 	data['cat_id'] = document.getElementsByName('cat_id')[0].defaultValue;
 	// 获取旧图路径
 	data['old_cat_pic'] = document.getElementsByName('old_cat_pic')[0].defaultValue;
 	// 获取分类标题
 	data['cat_name'] = document.getElementsByName('cat_name')[0].defaultValue;
 	// 分类描述
 	data['cat_desc'] = document.getElementsByName('cat_desc')[0].defaultValue;
 	// 分类图标
 	data['cat_pic'] = document.getElementsByName('cat_pic')[0].files[0];
 	console.log(data);
 	for(var i=0;i<data.length;i++) {
 		// console.log(data[i].value);
 		 console.log(data[i]);
 	}
 	return false;
 	// 获取更新页面的数据
 	// $$.request({
 	// 	method:'post',
 	// 	url:"<{framework\core\Factory::U('admin/Category/addHandle')}>",
 	// 	data:'',
 	// 	dataType:'json',
 	// 	callback:function(result){
 	// 		console.log(result);
 	// 	}
 	// });
 }
