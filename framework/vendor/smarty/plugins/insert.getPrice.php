<?php
   /*
    * 获得商品价格的函数
    */
    function smarty_insert_getPrice($param)
    {
        //echo '<pre>';
        //var_dump($param);
        $id = $param['id'];
        //查询该id对应的商品价格
        $sql = "SELECT shop_price FROM goods WHERE goods_id=$id";
        //声明使用的是全局空间的dao对象
        global $dao;
        $result = $dao -> fetchColumn($sql);
        if($result){
            return $result;
        }
    }