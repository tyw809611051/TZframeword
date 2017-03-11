
//封装一个通用的方法，兼容各个版本的浏览器
//参数1：给哪个标签监视事件
//参数2：监视的事件类型
//参数3：事件发生时执行的函数
function bindEvent(element,eventType,fn){
	if(window.addEventListener){
		element.addEventListener(eventType, fn, false);
	}else{
		element.attachEvent("on"+eventType, fn);
	}
}

//封装一个简化document.getElementById();
function $(id){
	//根据id属性的值返回其对应的对象
	return document.getElementById(id);
}

//封装的拖拽效果
//鼠标按下事件
//参数1：拖拽的元素
//参数2：
function drag(obj,parent,content,box){
	obj.onmousedown = function(){
		//鼠标按下的时候，再来移动，就会产生拖拽的效果
		obj.onmousemove = function(e){
			var ev = e || window.event;
			var mouseX = ev.clientX;
			var mouseY = ev.clientY;
			//想让图片跟着鼠标移动，只需要让图片的坐标等于鼠标的坐标
			var imgX = mouseX - parent.offsetLeft -obj.clientWidth/2;
			var imgY = mouseY - parent.offsetTop - obj.clientHeight/2;

			//判断图片是否出界
			if(imgX <= 0){
				imgX = 0;
			}
			if(imgY <=0 ){
				imgY = 0;
			}
			if(imgX >= parent.clientWidth - obj.clientWidth){
				imgX = parent.clientWidth - obj.clientWidth;
			}
			if(imgY >= parent.clientHeight - obj.clientHeight){
				imgY = parent.clientHeight - obj.clientHeight;
			}

			obj.style.left = imgX+'px';
			obj.style.top = imgY+'px';

			//获得滑轮拖拽的距离
			var btnLeft = obj.offsetLeft;
			// btnLeft / (odrag.clinetWidth - btn.clientWidth) == contentTop  / (content.offsetHeight -box.offsetHeight )
	 		var contentTop = btnLeft / (parent.clientWidth - obj.clientWidth) * (content.offsetHeight -box.offsetHeight );
	 		content.style.top = -contentTop+'px';

		}
		//在整个body的范围内，只要鼠标抬起，就阻止移动的行为
		// oImg.onmouseup = function(){
		// 	//取消移动事件
		// 	oImg.onmousemove = null;
		// }
		document.onmouseup = function(){
			//取消移动事件
			obj.onmousemove = null;
		}
		//阻止鼠标按下时，可能出现的浏览器的默认行为
		return false;
	}
}


// alert(opacity);
function getStyle(obj,attr){
	//返回该属性的值
	if(obj.currentStyle){
		//说明是ie浏览器
		return obj.currentStyle[attr];	//如果属性是一个变量的时候，使用 []语法表示对象属性语法
	}else{
		return getComputedStyle(obj,false)[attr];
	}
}

//参数1：执行动画的元素
//参数2：执行动画的css属性们
//参数3：回调函数

function animation(obj,json,fn){
	clearInterval(obj.timer);
	obj.timer = setInterval(function(){
		//默认是到达目的地了
		var flag = true;
		//遍历每个属性
		for(var attr in json){
			//让每个属性执行动画
			if(attr == 'opacity'){
				var now = parseInt(getStyle(obj,attr) * 100);
			}else{
				var now = parseInt(getStyle(obj,attr));
			}
			//计算速度
			var speed = (parseInt(json[attr]) - now)/10;
			speed = speed > 0 ? Math.ceil(speed) : Math.floor(speed);

			if(now != parseInt(json[attr])){
				//有任何一个属性还没有到达目的地，就等着
				flag = false;
			}

			//开始加、减
			if(attr =='opacity'){
				obj.style[attr] = (now + speed)/100;
			}else{
				obj.style[attr] = now + speed +'px';
			}
		}
		if(flag){
			//说明所有的属性都到达目的地了
			clearInterval(obj.timer);
			if(fn){
				fn();
			}
		}

	}, 50)
}

//删除某个标签上面的class属性值
//参数1：操作的元素对象
//参数2：删除的class类名
//例如: <div id="content" class="page show">  ---> <div id="content" class="page show hide">
function removeClass(element,cName){
	//获得现在element身上的class属性的值，通过dom对象的className属性获得class属性的值
	var clsName = element.className;	//page show hide  目的：删除show
	// alert(clsName);
	var arr = clsName.split(' ');		//['page','show','hide']
	for(var i=0;i<arr.length;i++){
		//判断该对象是否存在删除的类cName
		if(arr[i] == cName){
			//将其删除即可，数组的函数  splice
			arr.splice(i,1);			//['page','hide']
		}
	}
	//将数组转换成字符串再赋值给element对象
	clsName = arr.join(' ');		// "page hide"
	element.className = clsName;	
}

//给某个标签添加class属性
//参数1：标签对象
//参数2：添加的类名
//例如：<div id="content" class="page">  ---->	<div id="content" class="page show">
function addClass(element,clsName){
	//先查找一下当前对象身上是否有clsName类
	var cName = element.className;
	if(!cName){
		//目前没有class属性
		element.className = clsName;
		return;
	}
	//存在class属性，还要判断该元素是否存在clsName这个属性
	var arr = cName.split(' ');
	for(var i=0;i<arr.length;i++){
		if(arr[i] == clsName){
			//说明现在就有clsName的值,不用添加、直接停止
			return;
		}
	}
	//执行到这里，说明没有clsName这个值
	element.className += ' '+clsName;
}


//封装通过类名查找节点对象
//封装一个通过类名查找元素的方法(实际开发的时候，id用的少.class用的多)
//参数1：查找的父元素，在哪个父标签中查找
//参数2：类名，查找的类
function getByClass(oParent,clsName){
	//先找到父元素中的所有的标签, * 通配符，代表所有的标签
	var childs = oParent.getElementsByTagName('*');
	//判断，哪个标签的className属性的值是prev，说明这个标签就是我们要查找的
	var arr = [];
	for(var i=0;i<childs.length;i++){
		if(childs[i].className == clsName){
			arr.push(childs[i]);
		}
	}
	return arr;
}


//封装ajax操作(支持get、post请求)
var $$ = {
	request:function(opt){
		//1. 获得xhr对象
		try{
			var xhr = new XMLHttpRequest();
		}catch(e){
			var xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
		//确定get还是post
		//确定请求的地址
		if(opt.method=='get'){
			var data = encodeURIComponent(opt.data);
			xhr.open(opt.method,opt.url+'?'+data+'&'+Math.random(),true);
			xhr.send();
		}else if(opt.method=='post'){
			xhr.open(opt.method,opt.url,true);
			xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			xhr.send(opt.data);
		}
		
		xhr.onreadystatechange = function(){
			if(xhr.readyState==4 && xhr.status==200){
				//说明请求完成并成功
				// alert(xhr.responseText);	
				if(opt.dataType=='xml'){
					
					opt.callback(xhr.responseXML);

				}else if(opt.dataType=='json'){

					eval("var obj = "+xhr.responseText);
					opt.callback(obj);

				}else if(opt.dataType=='text'){

					opt.callback(xhr.responseText);

				}
			}
		}

	}
}
//调用的时候传递参数（多个参数使用{}对象的形式传递）




