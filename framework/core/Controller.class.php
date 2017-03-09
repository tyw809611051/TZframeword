<?php
namespace framework\core;
use Smarty;
class Controller {
    /* 模板对象 */
    protected $_smarty;
   
    /* 初始化模板信息 */
    public function __construct() {
        
        $this->initSession();
        
        $this->initSmarty();
 
    }
    
    /* 初始化开启session */
    private function initSession() {
        //session_start();
    }
    /* 初始化模板信息 */
    private function initSmarty() {
        
        /* 新建模板对象 */
        $this->_smarty = new Smarty();
        
        /* 设置模板目录 */
        
        $this->_smarty->setTemplateDir(APP_PATH.MODULE.'/view/');
        /* 设置编译目录 */
        $this->_smarty->setCompileDir(APP_PATH.'runtime/tpls_c');
        
        /* 设置定界符 */
        $this->_smarty->left_delimiter= '<{';
        $this->_smarty->right_delimiter= '}>';
    }
    
    /* 设置跳转目录 */
    public function jumpURL($message='',$url,$delay=3) {
        echo $message.'<br>';
        header("Refresh:$delay;url=$url");
        die;
    }
}