<?php
    namespace framework\dao;
    //如何定义接口,接口不实现功能，只是定义有哪些功能
    interface I_DAO
    {
        //查询所有数据
        public function fetchAll($sql);
        
        //查询一条记录
        public function fetchRow($sql);
        
        //查询一个字段的值
        public function fetchColumn($sql);
        
        //执行增删改的操作
        public function exec($sql);
        
        //返回刚刚执行插入语句返回主键的值
        public function lastInsertId();
        
        //执行增删改受影响的记录数
        public function affectedRows();
    	
        //安全处理，对引号转义并包裹
        public function quoteValue($data);
    }