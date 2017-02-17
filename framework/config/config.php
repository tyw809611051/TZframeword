<?php
/*
 * 框架的配置文件，通常保存一些框架的基本信息
 */
return array(
    //数据库的信息
    'host'      =>  '',
    'dbname'    =>  '',
    'port'      =>  3306,
    'user'      =>  '',
    'pwd'       =>  '',
    'charset'   =>  'utf8',
	'prefix'	=>	'',		//数据表的前缀
		
    //框架的信息
    'default_module'    =>  'home',     //默认的模块是前台
    'default_controller'=>  'index',    //默认的控制器是index控制器
    'default_action'    =>  'indexAction',     //默认的动作是index方法
    
);