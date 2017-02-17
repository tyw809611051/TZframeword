<?php
namespace framework\tools;
/*
 * 分页类
 */
class Page
{
    //成员属性
    private $_total;              //总的结果数
    private $_page_size;            //每页显示几条记录
    private $_page_now = 1;             //当前是第几页
    private $_url;                 //默认点击分页时跳转的url地址
    private $_where = '';           //分页时携带的条件
    
    public function __set($p,$v)
    {
        //如果该对象有$p这个属性，就设置该属性的值
        if(property_exists($this, $p)){
            $this -> $p = $v;
        }
    }
    public function __get($p)
    {
        //如果$p这个属性有值就返回这个变量的值
        if(isset($this->$p)){
            return $this -> $p;
        }
    }
    
    //成员方法
    public function create()
    {
        $url = $this -> _url.'/page/';
        //默认是第一页
        $first = 1;
        //首页
        $first_active = $this->_page_now == 1?'active':'';
        $html = <<<PAGEHTML
        <ul class="pagination">
            <li class="$first_active">
            <a href="{$url}{$first}{$this->_where}">首页</a>
            </li>
PAGEHTML;
        
        //分页列表，循环生成
        //总的页数
        $page_count = ceil($this -> _total / $this -> _page_size);
        for($i=$this->_page_now-3;$i<=$this->_page_now+3;$i++){
            //因为$i = 1的时候是首页，而且首页时固定的
            //因为$i=34最后一页的时候，后面也就不用再追加li标签，直接停止
            if($i<=1 || $i>=$page_count){
                continue;
            }
            //如果当前页等于$i
            $active = $this->_page_now == $i ? 'active' :'';
            
            $html .= <<<PAGEHTML
            <li class="$active">
                <a href="{$url}{$i}{$this->_where}">$i</a>
            </li>
PAGEHTML;
        }    
        
        //尾页，最后一页
        $last_active = $this->_page_now == $page_count ? 'active':'';
        $html.= <<<PAGEHTML
        <li class="$last_active">
        <a href="$url$page_count{$this->_where}">尾页</a>
        </li>
        </ul>
PAGEHTML;
        return $html;
    }
}