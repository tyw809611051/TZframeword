<?php
namespace framework\tools;
use Finfo;
/*
 * 文件上传类
 * 作者:
 * 时间：
 */
class Upload
{
    //成员属性
    private $_upload_path;              //上传的路径
    private $_max_size = 2*1024*1024;   //上传的文件最大限制
    private $_prefix;                   //上传到服务器的文件的前缀
    private $_allow_type = array('.jpg','.png','.gif'); //允许的文件后缀
    //允许的文件的mime类型
    private $_allow_mime_type =
    array('image/png', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
    
    //提供对应的set方法，允许用户在类的外面设置属性的值
    public function setUploadPath($upload_path)
    {
        $this -> _upload_path = $upload_path;
    }
    public function setMaxSize($max_size)
    {
        $this -> _max_size = $max_size;
    }
    public function setPrefix($prefix)
    {
        $this -> _prefix = $prefix;
    }
    public function setAllowType($types)
    {
        $this -> _allow_type = $types;
    }
    public function setAllowMimeType($mimes)
    {
        $this -> _allow_mime_type = $mimes;
    }
        
    //成员方法
    public function doUpload($fileinfo)
    {
        //判断用户是否上传了文件
        if($fileinfo['error']!=0){
            echo '请选择上传的文件';
            exit;
        }
        //参数1：临时的文件
        //参数2：目的地
        //接收文件（二进制流数据）使用$_FILES接收
        //1. 限制上传的文件的大小不能超过2MB
        if($fileinfo['size']>$this->_max_size){
            echo '文件大小不合法<br>';
            exit; //因为不需要返回结果，而仅仅是让代码在这里停止
        }
        //2. 随机生成文件名，防止上传同一张图片时文件名冲突
        
        $file_name = uniqid($this->_prefix,true);
        //拼接后缀,认为一个文件最后一次出现的.后面的就是后缀
        $ext = strrchr($fileinfo['name'], '.');
    
        //3. 以日期格式创建子目录
        $sub_path = date('Ymd').'/';
        if(!is_dir($this->_upload_path.$sub_path)){
            mkdir($this->_upload_path.$sub_path,0777,true);
        }
        $file_name = $this->_upload_path.$sub_path.$file_name . $ext;
        //die($file_name);
    	
        //4. 验证用户上传的文件类型
        
        //获得用户上传的文件类型
        if(!in_array($ext,$this->_allow_type)){
            echo '文件类型不支持';
            exit;
        }
        //验证mime类型（网络中传输文件时的文档类型）
        
        if(!in_array($fileinfo['type'], $this->_allow_mime_type)){
            echo '文件类型不支持';
            exit;
        }
        //通过Finfo获得文件的类型
        $finfo = new Finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo -> file($fileinfo['tmp_name']);
        //die($mime_type);
        if(!in_array($mime_type, $this->_allow_mime_type)){
            echo '文件类型不支持';
            exit;
        }
        
        if(move_uploaded_file($fileinfo['tmp_name'], $file_name)){
            return $file_name;
        }else{
            return false;
        }
    }
}