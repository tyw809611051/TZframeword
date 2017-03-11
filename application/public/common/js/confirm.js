 function firmdelete() {
            return window.confirm('确认删除');
        }
/**************分类效果***************/
// 验证分类添加
function verifyCatAdd() {
	// 获取验证表单元素
	var oName = document.getElementsByName('cat_name')[0];
	var oDesc = document.getElementsByName('cat_desc')[0];

	// 判断是否为空
	if(oName.value == '') {
		alert('分类名称不能为空！');
		return false;
	} else if(oDesc.value == '') {
		alert('分类描述不能为空!');
		return false;
	} else {
		return true;
	}
}


// 验证文章添加
function verifyArtAdd() {
	// 获取验证表单元素
	var oTitle = document.getElementsByName('art_title')[0];
	var oContent = document.getElementById('editor');
	// 富文本待处理
	if(oTitle.value == '') {
		alert('文章标题不能为空');
		return false;
	} else {
		return true;
	}

}