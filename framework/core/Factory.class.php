<?php
namespace framework\core;

class Factory {
    static public $_all_model = array();
    /*工厂模式单例 生产对象*/
    static public function M($model) {
        //判断一下用户传递过来的类前面是否有 \ ，如果有就表示实例化的其他空间的类，
        //如果没有就表示CategoryModel
        //strchr()查找某个字符在整个字符串中第一次出现的位置
        if(strchr($model, '\\')){
            //admin\model\CategoryModel
            $model_name = $model;
        }else{
            //CategoryModel
            $model_name = MODULE.'\\model\\'.$model;
        }
        //允许用户实例化模型时可以不写Model，Factory::M('Category')
        //如果用户没有写Model，我们手动拼接上Model
        //从后面向前面数5个索引，截取5个长度
     /*    if(substr($model_name, -5,5)!='Model'){
            $model_name .= 'Model';
        } */
        if(!isset(self::$_all_model[$model]) 
            ||
           !self::$_all_model[$model] instanceof $model) {
           /* 加载模型路径 */
              /*  $model_name = MODULE.'\\model\\'.$model; */
               self::$_all_model[$model] = new $model_name;
            }
            
        return self::$_all_model[$model];
    }
}