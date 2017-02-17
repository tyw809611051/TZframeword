<?php
/*
 * 工厂类,目的就是实例化单例对象
 */
namespace framework\core;
class Factory
{
    //参数 $className就是类名
    //
    public static function M($className)
    {
    	//判断一下用户传递过来的类前面是否有 \ ，如果有就表示实例化的其他空间的类，
    	//如果没有就表示xxxModel
    	//strchr()查找某个字符在整个字符串中第一次出现的位置
    	if(strchr($className, '\\')){
    		//admin\model\xxxModel
    		$model_name = $className;
    	}else{
    		//xxxModel
    		$model_name = MODULE.'\\model\\'.$className;
    	}
    	//允许用户实例化模型时可以不写Model，Factory::M('xxx')
    	//如果用户没有写Model，我们手动拼接上Model
    	//从后面向前面数5个索引，截取5个长度
    	if(substr($model_name, -5,5)!='Model'){
    		$model_name .= 'Model';
    	}    	
        static $model_list = array();
        if(!isset($model_list[$className])){
            //new xxxModel,如果实例化的是后台的模型，应该：new admin\model\xxxModel
            $model_list[$className] = new $model_name;
        }
        return $model_list[$className];
    }

    /*
     * 封装生成伪静态url地址的U方法
     * @param   $path，模块、控制器、方法分别是谁 home/index/indexAction
     * @param	$params 传递的额外参数 array('page'=>7,'id'=>5)
     * @return /文件名/home/index/indexAction/page/7
     */
    public static function U($mca,$params)
    {
    	//当前脚本所在的位置，"/文件名/index.php"
    	$path = $_SERVER['SCRIPT_NAME'];
        var_dump($path);die;
    	//获得当前项目的根目录,这样如果将来项目的名称修改了，这里项目的根目录不用做任何改动
    	$base_path = str_replace('index.php', '', $path);
    	
    	$base_path .= $mca;
    	//判断是否有额外的参数
    	if($params){
    		foreach ($params as $k=>$v){
    			$base_path .= '/'.$k.'/'.$v;
    		}
    	}
		return $base_path;
    }
    
}
