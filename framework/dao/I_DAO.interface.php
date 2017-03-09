<?php
namespace framework\dao;

interface I_DAO {
// 2.执行非查询
public function exec($sql);
// 3.执行查询
public function query($sql);
// 4.执行查询获取单列数据
public function fetchColumn($sql);
// 5.执行查询获取单条记录数据
public function fetchRow($sql);
// 6.执行获取所有数据
public function fetchAll($sql);
// 7.查询受影响（删除和修改）的行数
public function affectRows();
// 8.返回插入的主键
public function lastInsertID();
// 9.安全处理，引号包裹
public function quoteValue($str);
}