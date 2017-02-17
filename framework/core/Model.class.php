<?php
/*
 * 这是基础模型类
 * 主要是封装其他模型间公共的代码
 */
namespace framework\core;
use framework\dao\DAOPDO;

class Model
{
    protected  $dao;
    protected  $true_table;		//真实的表名
    protected  $pk;				//主键字段
    protected $error = array();
    
    public function showError()
    {
    	$err_str = '';
    	foreach($this->error as $row){
    		$err_str .= $row.'<br>';
    	}
    	return $err_str;
    }    
    
    public function __construct()
    {
        $this -> initDAO();
        //初始化数据表实际的表名
        $this -> initTable();
        //初始化主键字段
        $this->initField();
    }
    //初始化dao对象的方法
    private function initDAO()
    {
        //插入数据
        $option = $GLOBALS['config'];
        $this -> dao = DAOPDO::getSingleton($option);
    }
    //初始化实际表（前缀+表名）
    private function initTable()
    {
    	if(isset($this->logic_table)){
    		$this -> true_table = $GLOBALS['config']['prefix'].$this->logic_table;
    		return;
    	}
    	//执行到这里，说明没有设置logic_table属性
    	//我们就得分析出来
    	//获得当前模型对象是哪个类实例化出来的
    	$className = get_class($this);
    	
    	//可以使用basename（）函数获得最后一个分隔符后面的内容
    	
    	$className = basename($className);
    	
    	//将Model替换掉
    	$table_name = str_replace('Model', '', $className);
    	//使用正则替换,先找到前面是小写字母的大写字母，在前面加上_
    	$reg = '/(?<=[a-z])([A-Z])/';
    	$table_name = preg_replace($reg, '_$1', $table_name);
    	
    	$this -> true_table = $GLOBALS['config']['prefix'].strtolower($table_name);
    }
    //初始化数据表的主键字段
    private function initField()
    {
    	$sql = "DESC `$this->true_table`";
    	$result = $this -> dao -> fetchAll($sql);
    	
    	foreach ($result as $key=>$v){
    		if($v['Key']=='PRI'){
    			//说明这个字段就是主键
    			$this -> pk = $v['Field'];
    		}
    	}
    }
    //封装自动插入的操作
    /* 参数[以ecshop为例]：
     * $data = array(
   	   		'goods_name'	=>	'诺基亚',
   	   		'shop_price'	=>	1800
   	   );
     */
    public function insert($data)
    {
    	//最终的sql：
    	//$sql = "insert into `ecs_goods`(`goods_name`,`shop_price`) VALUES('苹果','3900')";
    	$sql = "INSERT INTO `$this->true_table`";
    	//拼接字段名称（先获得数组的下标，数组的下标就是对应的字段名称）
    	$fields = array_keys($data);
    	//获得数组的值，通过array_values()
    	$values = array_values($data);
    	
    	//字段名称两边加上 `` 反引号（通过foreach循环给下标的两侧加上反引号）
    	//array_map()函数是使用参数1这个函数，对参数2这个数组进行处理，会将参数2数组元素的值一个一个的传递进去
    	$field_list = array_map(function($v){
    		return '`'.$v.'`';
    	}, $fields);
    	$field_list = '('.implode(',', $field_list).')';
    	$sql .= $field_list;
    	
    	//拼接sql语句中VALUES部分，说明：因为这一部分会将结果直接保存到数据库,在保存之前，我们要对数据进行安全处理
    	$field_values = array_map(array($this->dao,"quoteValue"), $values);
    	$field_values = implode(',', $field_values);
    	
    	$sql .= " VALUES($field_values)";
    	    	
    	$this -> dao -> exec($sql);
    	
    	return $this -> dao -> lastInsertId();
    }
	
    /*
     * delete($pk_value)
     * 自动删除的操作，参数就是删除的记录的主键的值
     */
    public function delete($pk_value)
    {
    	$sql = "DELETE FROM `$this->true_table` WHERE `$this->pk`=$pk_value";
    	return $this -> dao -> exec($sql);
    }
    /*
     * update()
     * 自动更新的操作，执行更新语句
     * 最终：UPDATE `goods` SET `goods_name`='诺基亚',`shop_price`=1900 WHERE `goods_id`=503
     * 参数1：更新的数据  array(goods_name=>'诺基亚',shop_price=>1900)
     * 参数2：更新的条件  array(goods_id=>503)
     */
    public function update($data,$where=null)
    {
    	//先判断一下条件是否为空，如果为空就不能更新
    	if(is_null($where)){
    		return false;
    	}else{
    		//不为空才能更新
    		$field = array_keys($where);
    		$value = array_values($where);
    		$where_str = '`'.$field[0].'`'."='{$value[0]}'";
    	}
    	//拼接更新的字段(字段名称要加上反引号、字段的值要使用安全处理，引号转义)
    	$fields = array_keys($data);
    	$values = array_values($data);
    	//字段名称使用反引号包裹
    	$fields_list = array_map(function($v){
    		return '`'.$v.'`';
    	},$fields);
    	//字段的值使用安全处理，引号转义
    	$values_list = array_map(array($this->dao,"quoteValue"),$values);
    	//拼接字段名称、字段的值
    	$field = '';
    	foreach($fields_list as $k=>$v){
    		$field .= $v.'='.$values_list[$k].',';
    	}
    	//"`goods_name`='诺基亚',`shop_price`='1900',"
    	//substr() 字符串截取，第三个参数是负数就表示从后面向前面数
    	$field = substr($field, 0,-1);
    	
    	$sql = "UPDATE `$this->true_table` SET $field WHERE $where_str";
    	
    	return $this -> dao -> exec($sql);
    }
    
    /*
     * find()
     * 自动查询的操作
     * 最终：SELECT `goods_name`,`shop_price` FROM `goods` WHERE `goods_name` = '诺基亚';
     * 参数1：查询的字段名称,如果为空的话，就表示查询所有的字段,例如：array('goods_name','shop_price')
     * 参数2：查询的条件，如果为空的话，就表示没有约束条件，查询所有数据，例如：array('goods_name'=>'诺基亚')
     */
    public function find($fields=null,$where=null)
    {
    	if(is_null($fields)){
    		$fields = '*';
    	}else{
    		$fields = array_map(function($v){
    			return '`'.$v.'`';
    		},$fields);
    		$fields = implode(',', $fields);
    	}
    	
    	//拼接where条件
    	if(is_null($where)){
    		$where_str = '';
    	}else{
    		foreach($where as $k=>$v){
    			//$k就是字段名称，$v就是字段的值
    			$str = '`'.$k.'`'."= ".$this->dao->quoteValue($v);
    		}
    		$where_str = "WHERE $str";
    	}
    	$sql = "SELECT $fields FROM `$this->true_table` $where_str";
    	
    	//执行sql语句
    	return $this -> dao -> fetchRow($sql);
    }
    
}