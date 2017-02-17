<?php
/*
 * 封装的框架的入口文件类
 */
namespace framework\core;
class Framework
{
    public function __construct()
    {
        //初始化路径常量
        $this -> initConst();
        
        //注册自动加载函数
        $this ->autoload(); 
        
        //先合并框架的配置 和 项目公共的配置
        $config1 = $this -> loadFrameConfig();
        $config2 = $this -> loadCommonConfig();
        $GLOBALS['config'] = array_merge($config1,$config2);
        
        //初始化模块
        
        //初始化伪静态的m模块、c控制器、a方法
        $this -> initPathInfo();
        
        $this -> initModule(); 
        $GLOBALS['config'] = array_merge($GLOBALS['config'],$this->loadModuleConfig());
        
        //初始化MCA
        $this -> initMCA();
        
        //分发任务
        $this -> initDispatch();
    }
    //初始化pathinfo
    private function initPathInfo()
    {
    	if(isset($_SERVER['PATH_INFO'])){
    		//获得url地址的信息
    		$path = $_SERVER['PATH_INFO'];	//"/home/index/indexAction.html"
    		 
    		//2. .html是障眼法，迷惑搜索引擎、用户，在这里没有实际作用，所以我们先替换掉
    		$postfix = strrchr($path, '.');
    		$path = str_replace($postfix, '', $path);	//	"/home/index/indexAction"
    		 
    		//3. 把第一个 / 替换掉,从索引为1的地方开始截取，如果不写第三个参数就表示截取到末尾
    		$path = substr($path, 1);				//	"home/index/indexAction"
    		 
    		//4. 将字符串根据  /  炸开
    		$arr = explode('/', $path);		//如果地址栏就是3个参数，可以获得mca
    		//思考：如果地址栏中不是3个参数的时候，怎么处理一下？
    		$length = count($arr);
    		if($length==1){
    			//确定模块
    			$_GET['m'] = $arr[0];
    		}elseif ($length==2){
    			//确定模块
    			$_GET['m'] = $arr[0];
    			//控制器
    			$_GET['c'] = $arr[1];
    		}elseif ($length==3){
    			//确定模块
    			$_GET['m'] = $arr[0];
    			//控制器
    			$_GET['c'] = $arr[1];
    			//方法
    			$_GET['a'] = $arr[2];
    		}else{
    			//如果参数大于3个，从第4个开始（下标为3的），每两个是一对  page=>6  id=>3
    			//确定模块
    			$_GET['m'] = $arr[0];
    			//控制器
    			$_GET['c'] = $arr[1];
    			//方法
    			$_GET['a'] = $arr[2];
    			
    			//生成这样的结果：$_GET['page'] = 6   $_GET['id']=3;
    			for ($i=3;$i<$length;$i+=2){
    				$_GET[$arr[$i]] = $arr[$i+1];
    			}

    		}
    	}
    }
    private function initConst()
    {
    	//定义目录的根路径getcwd 和 __DIR__是一模一样的，在这里纯粹是给大家扩展一个函数
    	define('ROOT_PATH',str_replace('\\', '/', getcwd().'/'));
    	//先定义应用程序的路径
    	define('APP_PATH',ROOT_PATH.'application/');
    	//定义框架的路径
    	define('FRAMEWORK_PATH',ROOT_PATH.'framework/');
    	//定义常量保存公共资源（css、img、js）的路径
    	define('PUBLIC_PATH','/文件名/application/public/');
    	//定义常量保存文件上传的路径
    	define('UPLOAD_PATH','./application/public/uploads/');
    	//定义图像压缩处理的保存路径
    	define('THUMB_PATH','./application/public/static/thumb/');
    	//保存公共的字体文件目录
    	define('FONT_PATH','./application/public/fonts/');
    	//静态文件的目录
    	define('STATIC_PATH','./application/public/static/html/');
    }
    //自动加载方法
    //自动加载(当我们访问一个不存在的类，也就是说需要一个类，但是这个类不存在，就会自动触发自动加载机制)
    //会将需要的类名以参数的形式传递到函数中
    private function userAutoload($className){
        //针对第三方的类我们做特例处理
        if($className=='Smarty'){
            require_once './framework/vendor/smarty/Smarty.class.php';
            return;
        }
        //根据需要的类名，拼接出完整的文件地址
        $arr = explode('\\', $className);
        if($arr[0]=='framework'){
            //根目录就是framework
            $base_dir = './';
        }else{
            $base_dir = './application/';
        }
        //拼接子目录   admin/controller/goodsController
        $sub_dir = str_replace('\\', '/', $className);
    
        //拼接后缀(有一个特例，就是接口后缀是.interface.php)
        if(substr($arr[count($arr)-1],0,2)=='I_'){
            //说明这是接口文件
            $fix = '.interface.php';
        }else{
            $fix = '.class.php';
        }
        $class_file = $base_dir.$sub_dir.$fix;
        if(file_exists($class_file)){
            //如果类文件存在，我们采取加载，也就是说这里加载的类都是我们定义好的，如果是其他的第三方的类，我们就不加载了
            require_once $class_file;
        }
    }
    
    //注册自动加载函数
    private function autoload()
    {   
        //如果回调函数是是一个函数的话，直接写函数名接口
        //现在参数是对象的方法的话，需要传递一个数组的形式array(对象,"方法的名称")
        spl_autoload_register(array($this,"userAutoload"));
    }

    //初始化模块（前台、后台）
    private function initModule()
    {
        //模块，前台还是后台
        $m = isset($_GET['m'])?$_GET['m']:$GLOBALS['config']['default_module'];
        define('MODULE', $m);
    }
    //初始化MCA
    private function initMCA()
    {
        //接收地址栏传递的参数       
        //确定控制器
        $c = isset($_GET['c'])?$_GET['c']:$GLOBALS['config']['default_controller'];
        define('CONTROLLER',$c);
        //确定访问的是哪个操作（控制器的哪个方法）
        $a = isset($_GET['a'])?$_GET['a']:$GLOBALS['config']['default_action'];
        //indexAction
        //先判断一下方法中是否有indexAction，如果没有写，我们在这里拼接上
        if(substr($a,-6)!='Action'){
        	$a .= 'Action';
        }
        define('ACTION', $a);        
    }
    
    //初始化分发控制器操作
    private function initDispatch()
    {
        //找到控制器类，实例化对象
        $controller_name = MODULE.'\\controller\\'.CONTROLLER.'Controller';
        //因为每个类都有自己的命名空间，所以使用的时候要加上命名空间
        $controller = new $controller_name;
        //调用控制器对象的方法
        $a = ACTION;
        $controller -> $a();
    }
    
    //加载框架的配置文件
    private function loadFrameConfig()
    {
        $config = require_once './framework/config/config.php';
        return $config;
    }
    //加载项目公共的配置文件
    private function loadCommonConfig()
    {
        $config = require_once './application/common/config/config.php';
        return $config;
    }
    //加载项目具体模块的配置文件
    private function loadModuleConfig()
    {
        $config = require_once './application/'.MODULE.'/config/config.php';
        return $config;
    }
}