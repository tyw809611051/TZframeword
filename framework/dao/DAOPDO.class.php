<?php
namespace framework\dao;

use framework\dao\I_DAO;
use PDO;
class DAOPDO implements I_DAO {
    /* 数据库信息 */
    private $_host;
    private $_user;
    private $_pwd;
    private $_port;
    private $_charset;
    private $_dbname;
    
    /* 保存连接数据库的属性 */
    private $_pdo_dao;
    /* 保存影响的行数 */
    private $_affectrows;
    /* 保存该类的对象 */
    static public $instance;
    
    /* 单例生产数据库对象 */
    static public function getSingleton(array $config) {
        
        if(!self::$instance instanceof self) {
            
            self::$instance = new self($config);
        }
        
        return self::$instance;
    }
    
    /* 私有化构造函数 */
    private function __construct($config) {
        
        /* 初始化数据库信息 */
        $this->initOption($config);
        /* 初始化数据库对象 */
        $this->initPDO();
     
    }
    
    private function initOption($config) {
        /* 初始化数据库信息 */
        $this->_host = !empty($config['host']) ? $config['host'] : '';
        $this->_user = !empty($config['user']) ? $config['user'] : '';
        $this->_pwd = !empty($config['pwd']) ? $config['pwd'] : '';
        $this->_port = !empty($config['port']) ? $config['port'] : 3306;
        $this->_charset = !empty($config['charset']) ? $config['charset'] : '';
        $this->_dbname = !empty($config['dbname']) ? $config['dbname'] : '';
        
    }
    
    private function initPDO() {
        
        /* 初始化数据库对象 */
        $dsn="mysql:host={$this->_host};port={$this->_port};dbname={$this->_dbname}";
        $user = $this->_user;
        $pwd = $this->_pwd;
        $this->_pdo_dao = new PDO($dsn,$user,$pwd);
        
        if(!$this->_pdo_dao) {
            echo '连接失败'.'<br>';
            $err = $this->_pdo_dao->errorInfo();
            echo '失败原因：'.$err[2];
            return false;
        }
        
    }
    
    /* 私有化克隆函数 */
    private function __clone() {}
    
    // 2.执行非查询
    public function exec($sql) {
        /* 返回影响的行数 */
        $row =  $this->_pdo_dao->exec($sql);
        if(!$row) {
            echo '<br>非查询执行失败';
            echo '<br>失败语句:'.$sql;
            $err = $this->_pdo_dao ->errorInfo();
            echo '<br>失败原因：'.$err[2];
            return false;
        }
        return $this->_affectrows = $row;
    }
    
    // 3.执行查询
    public function query($sql) {
        
        return $this->_pdo_dao->query($sql);
 
    }
    // 4.执行查询获取单列数据
    public function fetchColumn($sql) {
        
        $pdo_statement = $this->query($sql);
        /* 错误处理 */
        if(!$pdo_statement) {
            
           $this->setErr($sql);
           
        }
        /* 抓取一列数据 */
        $one = $pdo_statement -> fetchColumn();
        return $one;
 
    }
    // 5.执行查询获取单条记录数据
    public function fetchRow($sql) {
        
        $pdo_statement = $this->query($sql);
        /* 错误处理 */
        if(!$pdo_statement) {
        
            $this->setErr($sql);
             
        }
        
        /* 抓取一行数据 */
        $row = $pdo_statement->fetch(PDO::FETCH_ASSOC);
        return $row;
        
    }
    // 6.执行获取所有数据
    public function fetchAll($sql) {
        
        $pdo_statement = $this->query($sql);
        /* 错误处理 */
        if(!$pdo_statement) {
        
            $this->setErr($sql);
             
        }
        
        /* 抓取一行数据 */
        $rows = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
        
    }
    // 7.查询受影响（删除和修改）的行数
    public function affectRows() {
        $affect = $this->_affectrows;
        /* 每次清空影响的行数 */
        $this->_affectrows = null;
        return $affect;
    }
    // 8.返回插入的主键
    public function lastInsertID() {
        
      return $this->_pdo_dao->lastInsertId();
        
    }
    // 9.安全处理，引号包裹
    public function quoteValue($str) {
        
        return $this->_pdo_dao->quote($str);
    }
    
    /* 错误处理 */
    public function setErr($sql) {
        echo '执行失败';
        echo '失败语句：'.$sql;
        $err = $this->_pdo_dao->errorInfo();
        echo '失败原因：'.$err[2];
        return false;
    }
}
