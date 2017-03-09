<?php
namespace framework\core;

class Framework {
    
    public function __construct() {
        $this->autoload();
        $config1 = $this->loadFrameConfig();
        $config2 = $this->loadCommonConfig();

        $GLOBALS['config'] = array_merge($config1,$config2);
        
        $this->initModule();
        
        $config3 = $this->loadModuleConfig();
        $GLOBALS['config'] = array_merge($GLOBALS['config'],$config3);
        
        $this-> initCA();
        
        $this->initDispath();
        
    }
    /* 注册自动加载函数 */
    public function autoload() {
        spl_autoload_register(array($this,"userAutoload"));
    }
    
    /* 实现自动加载函数 */
    private function userAutoload($className) {
        /* 判断是否是第三方文件 */
        if($className == 'Smarty') {
            require FRAME_PATH.'vendor/smarty/Smarty.class.php';
        }
        
        /* 分割为数组 */
        $arr = explode('\\', $className);
        /* 判断属于哪个平台还是框架 */
        if($arr[0] == 'framework') {
            $base_dir = './';
        } else {
            $base_dir = './application/';
        }
        /* 转换为Linux分割符 */
        $sub_dir = str_replace('\\', '/', $className);
        /* 判断文件后缀名 */
        if(substr($arr[count($arr)-1],0,2) == 'I_') {
            $prefix = '.interface.php';
        } else {
            $prefix = '.class.php';
        }
        /* 拼接文件名 */
        $class_name = $base_dir.$sub_dir.$prefix;
      
        /* 判断是否为我们所写文件 */
        if(file_exists($class_name)) {
            require $class_name;
        }
    }
    /* 设置模块 */
    public function initModule() {
        $m = isset($_REQUEST['m'])? $_REQUEST['m'] : 'admin';
        define('MODULE', $m);
    }
    /* 设置控制器和方法 */
    public function initCA() {
        $c = isset($_REQUEST['c']) ? $_REQUEST['c'] : 'Category';
        define('CONTROLLER', $c);
        $a = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'index';
        define('ACTION', $a);
        
    }
    /* 设置前端请求分发器 */
    public function initDispath() {
      //找到控制器类，实例化对象
        $controller_name = MODULE.'\\controller\\'.CONTROLLER.'Controller';
        //因为每个类都有自己的命名空间，所以使用的时候要加上命名空间
        $controller = new $controller_name;
        
        $action = ACTION.'Action';
       
        /* 调用方法 */
        $controller ->$action();
        
    }
    
    /* 加载框架配置文件*/
    public function loadFrameConfig() {
        return require FRAME_PATH.'config/config.php';
    }
    /* 加载公共配置文件 */
    public function loadCommonConfig() {
        return require APP_PATH.'common/config/config.php';
    }
    /* 加载模块独有配置文件 */
    public function loadModuleConfig() {
        return require APP_PATH.MODULE.'/config/config.php';
    }
}