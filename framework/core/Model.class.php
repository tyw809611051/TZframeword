<?php
namespace framework\core;

use framework\dao\DAOPDO;
class Model {
// 保存数据库的对象
protected $_dao;
// 调用该模型的表名
protected $_true_table;
// 调用该模型的主键
protected $_true_pk;
 /* 保存错误信息 */
protected $_error = array();


// 循环遍历错误
public function showErr() {
    $err_str = '';
    /*循环遍历错误信息  */
    foreach ($this->_error as $v) {
        $err_str = $v;
    }
    return $err_str;
}

// 构造函数初始化数据库对象，表名，主键
public function __construct() {
    /* 初始化数据库对象 */
    $this->initPDO();
    /* 初始化数据库表*/
    $this->initTable();
    /* 初始化数据表主键 */
    $this->initFiled();
}
//初始化数据库的对象
private function initPDO() {
    $config = array(
        'host'  =>  '',
        'user'  =>  '',
        'pwd'   =>  '',
        'port'  =>  ,
        'charset'   =>  '',
        'dbname'    =>  '',
    );
    /* 创建数据库对象 */
    $this->_dao = DAOPDO::getSingleton($config);
}

//初始化数据库主表
private function initTable() {
    
    /* 调用该基础模型的数据表 */
    $this->_true_table = $this->_logic_table;
    
}

//初始化数据表主键
private function initFiled() {
    /* 查询表结构的语句 */
    $sql = "DESC `$this->_true_table`";
    /* 发送sql语句,查询所有字段 */
    $row = $this->_dao->fetchAll($sql);
    
    foreach ($row as $k => $v) {
        
        if($v['Key'] == 'PRI') {
            
            $this->_true_pk = $v['Field'];
        }
    }
    
}

// 自动增删改查

/**
 * 〈自动更新数据〉
 * <insert into 表名  （字段1，字段2） values(值1，值2)>
 * @param [$data]     [带有字段和值的关联数组]
 * @return[int:插入的主键]
 */
public function insert($data) {

    /* 将数组解析为键 */
    $filed_key = array_keys($data);
        //循环数组，加上反引号
        $filed_key = array_map(function($v) {
            return '`'.$v.'`';
        }, $filed_key);
        //将数组转换为字符串,并加上括号
        $key = '('.implode(',', $filed_key).')';
        
    /* 将数组解析为值 */
        $filed_values = array_values($data);
        //循环数组，加上包裹
        $filed_values = array_map(array($this->_dao,"quoteValue"), $filed_values);
        //将数组转换为字符串,并加上括号
        $values = '('.implode(',', $filed_values).')';
        
        /* 拼接sql语句 */
        $sql = "INSERT INTO `$this->_true_table` $key VALUES$values";
      
        /*  执行非查询sql语句*/
        $this->_dao ->exec($sql);
        /*  返回插入的主键*/
        return $this->_dao->lastInsertID();
        
}

/**
 * 〈自动更新操作〉
 * 〈updata 表名 set 字段1=值，字段2=值 where 字段=值〉
 * @param [$data]     [更新的字段，关联数组]
 * @param [$where]     [更新的条件,关联数组]
 * @return[int：影响的行数]
 */
public function update($data,$where=null) {
    
    /* 判断条件是否为空，为空则退出 */
    if(is_null($where)) {
        return false;
    } else {
        /* 不为空，则解析键和值 */
        $where_key = array_keys($where);
        $where_value = array_values($where);
        /* 将字段手动加上引号并组合 */
        $where_str = '`'.$where_key[0].'`'.'='.$where_value[0];
    }

    /* 解析字段数组为键和值 */
    $fileds_key = array_keys($data);
    $fileds_values = array_values($data);
        /* 字段名称使用反引号 */
        $fileds_key = array_map(function($v) {
            return '`'.$v.'`';
        }, $fileds_key);
        /* 值使用引号转义 */
        $fileds_values = array_map(array($this->_dao,"quoteValue"), $fileds_values);
    
    /* 循环 拼接 字段和值 */
        $fileds_str = '';
        foreach($fileds_key as $k => $v) {
            $fileds_str .=  $v.'='.$fileds_values[$k].',';
        }
    /* 去除最后一个逗号 */
        $filed = substr($fileds_str, 0,-1);
    
    /* 拼接sql语句 */
        $sql = "UPDATE `$this->_true_table` SET $filed WHERE $where_str";
       
    /* 执行sql语句 ，并返回影响的行数*/
   return $this->_dao->exec($sql);
   
}

/**
 * 〈自动删除操作〉
 * 〈delete from 表名 where 主键=id〉
 * @param [$id]     [主键的id值]
 * @return[int:返回影响的行数]
 */
public function delete($id) {
    /* 拼接sql语句 */
    $sql = "DELETE FROM `$this->_true_table` WHERE `$this->_true_pk`=$id";
    
    return $this->_dao ->exec($sql);
}

/**
 * 〈自动查询操作〉
 * 〈select *(字段1，字段2) from 表名 where 字段=值〉
 * @param [$data]     [仅仅包含字段的索引数组]
 * @param [$where]     [包含条件的关联数组]
 * @return[array:一行记录]
 */
public function selectRow($data=null,$where=null) {
    
    /*判断查询的字段数组是否为空  */
    if(is_null($data)) {
        $fileds = '*';
    } else {
        
        /* 不为空，将字段加反引号，转换为字符串*/
        
        $fileds = array_map(function($v) {
            return '`'.$v.'`';
        }, $data);
        
        $fileds = implode(',', $fileds);
    }
        
    /* 判断查询的条件是否为空 */
    if(is_null($where)) {
        $where_str = '';
    } else {
        /* 不为空;循环转义和包裹 */
        $where_str = '';
        foreach ($where as $k => $v) {
            $where_str .= '`'.$k.'`'.'='.$this->_dao->quoteValue($v);
        }
        
        /* 加上where字段 */
        $where_str = "WHERE ".$where_str;
        
        /* 拼接sql语句 */
        $sql = "select $fileds from `$this->_true_table` $where_str";
       
        /*  执行*/
        return $this->_dao->fetchRow($sql);
    }
       
}



}
