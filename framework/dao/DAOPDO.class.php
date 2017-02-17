<?php
    namespace framework\dao;
    use framework\dao\I_DAO;
    use PDO;
    require_once 'I_DAO.interface.php';
    
    class DAOPDO implements I_DAO
    {

        //私有的静态属性保存实例化的对象
        private static $instance; 
        //保存执行增删改受影响的记录数
        private $affectedRows;
        
        //私有的构造方法
        private function __construct($option)
        {
            //初始化数据库连接、PDO对象等
            $this -> initOptions($option);
            
            //初始化pdo对象
            $this -> initPDO();
        }
        private function initOptions($option)
        {
            //初始化属性，给属性赋值
            $this -> host = isset($option['host'])?$option['host']:'';
            $this -> dbname = isset($option['dbname'])?$option['dbname']:'';
            $this -> port = isset($option['port'])?$option['port']:3306;
            $this -> user = isset($option['user'])?$option['user']:'';
            $this -> pwd = isset($option['pwd'])?$option['pwd']:'';
            $this -> charset = isset($option['charset'])?$option['charset']:'utf8';
        }
        private function initPDO()
        {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
            $user = $this->user;
            $pwd = $this->pwd;
            $this -> pdo = new PDO($dsn,$user,$pwd);            
        }
        //私有的克隆方法，防止对象复制、克隆
        private function __clone()
        {
            
        }
        //公共的实例化对象的方法
        public static function getSingleton($option)
        {
            if(!self::$instance instanceof self){
                self::$instance = new self($option);
            }
            return self::$instance;
        }
        
        //查询所有数据
        public function fetchAll($sql){ 
            //将通用的代码封装到query方法中去
            $pdo_statement = $this -> query($sql);
            //当sql语句出错的话，query方法返回false
            if($pdo_statement == false){
                //输出错误信息
                $err_info = $this->pdo -> errorInfo();
                $err_str = $err_info[2];
                echo $err_str;
                return false;
            }
            return $pdo_statement -> fetchAll(PDO::FETCH_ASSOC);
        }
        
        //查询一条记录
        public function fetchRow($sql){
            //将通用的代码封装到query方法中去
            $pdo_statement = $this -> query($sql);
            //当sql语句出错的话，query方法返回false
            if($pdo_statement == false){
                //输出错误信息
                $err_info = $this->pdo -> errorInfo();
                $err_str = $err_info[2];
                echo $err_str;
                return false;
            }
            return $pdo_statement -> fetch(PDO::FETCH_ASSOC);            
        }
        
        //查询一个字段的值
        public function fetchColumn($sql){
            //将通用的代码封装到query方法中去
            $pdo_statement = $this -> query($sql);
            //当sql语句出错的话，query方法返回false
            if($pdo_statement == false){
                //输出错误信息
                $err_info = $this->pdo -> errorInfo();
                $err_str = $err_info[2];
                echo $err_str;
                return false;
            }
            return $pdo_statement -> fetchColumn();
        }
        public function query($sql)
        {
            return $this -> pdo -> query($sql);
        }
        
        //执行增删改的操作
        public function exec($sql){
            //注意：如果没有删除的结构返回受影响的记录数是0
            $result = $this -> pdo -> exec($sql);       

            if($result === false){
                //输出错误信息
                $err_info = $this -> pdo -> errorInfo();
                $err_str = $err_info[2];
                echo $err_str;
                return false;
            }
            //将受影响的删除、修改的记录数保存起来
            $this -> affectedRows = $result;
            return $result;
        }
        
        //返回刚刚执行插入语句返回主键的值
        public function lastInsertId(){
            return $this -> pdo -> lastInsertId();
        }
        
        //执行增删改受影响的记录数()
        public function affectedRows(){
            return $this->affectedRows;
        }
        //引号转义并包裹
        public function quoteValue($data)
        {
        	return $this -> pdo -> quote($data);
        }
    }