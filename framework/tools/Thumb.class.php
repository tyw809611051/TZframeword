<?php
namespace framework\tools;
/*
 * 封装图像压缩处理类
 */
class Thumb
{
    private $_filename;     //待压缩处理的文件
    private $_thumb_path;   //压缩之后保存的路径
    private $_mime;         //图像的mime类型
    //保存创建图像资源的函数列表
    private $creat_func = array(
        'image/png'     =>  'imagecreatefrompng',
        'image/jpeg'    =>  'imagecreatefromjpeg',
        'image/gif'     =>  'imagecreatefromgif'
    );
    //保存图像类型和生成图像资源的映射关系
    private $output_func = array(
        'image/png'     =>  'imagepng',
        'image/jpeg'    =>  'imagejpeg',
        'image/gif'     =>  'imagegif'
    );
    //new对象时传递的参数会自动传递到构造函数中
    public function __construct($filename)
    {
        if(!file_exists($filename)){
            echo '请输入正确的文件';
            return false;
        }
        $this -> _filename = $filename;
        //初始化该文件的mime类型
        $this -> _mime = getimagesize($filename)['mime'];
    }
    //根据图像资源类型获得对应的创建函数
    private function get_create_func()
    {
        //根据图像资源获得对应的创建函数
        return $this->creat_func[$this->_mime];
    }
    //根据图像资源获得对应的输出、生成函数
    private function get_output_func()
    {
        return $this->output_func[$this->_mime];
    }
    public function setThumbPath($path)
    {
        $this -> _thumb_path = $path;
    }
    
    //制作压缩图像
    public function makeThumb($area_w,$area_h)
    {
        //原理：将原图进行压缩，压缩之后拷贝到目标图像资源里面
        //参数2：原图资源
        $create_func = $this->get_create_func();
        $src_image = $create_func($this -> _filename);
        //参数3：拷贝到目标图像资源中的x轴落脚点
        $dst_x = 0;
        //参数4：拷贝到目标图像资源中的y轴落脚点
        $dst_y = 0;
        //参数5：原图的裁剪时的x轴落脚点
        $src_x = 0;
        //参数6：原图的裁剪时的y轴落脚点
        $src_y = 0;
        
        //参数9：原图的宽度
        $src_w = imagesx($src_image);
        //参数10：原图的高度
        $src_h = imagesy($src_image);
        
        //参数7：目标图像的宽度
        //如果原图的宽/范围的宽    小于  原图的高/范围的高
        //1000*800    100*50
        if($src_w/$area_w < $src_h/$area_h){
            $scale = $src_h/$area_h;            
        }
        if($src_w/$area_w >= $src_h/$area_h){
            $scale = $src_w/$area_w;
        }
        $dst_w = (int)$src_w / $scale;
        $dst_h = (int)$src_h / $scale;
        
        $image = imagecreatetruecolor($dst_w, $dst_h);
        
        //参数8：目标图像的高度

        //针对png图像背景透明化处理
        $color = imagecolorallocate($image, 255, 255, 255);
        //将白色透明化处理
        imagecolortransparent($image,$color);
        imagefill($image, 0, 0, $color);
        
        imagecopyresampled($image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        
        //告诉浏览器
        $sub_path = date('Ymd').'/';
        if(!is_dir($this->_thumb_path.$sub_path)){
            mkdir($this->_thumb_path.$sub_path,0777,true);
        }
        //确定文件名称
        $filename = 'thumb_'.basename($this->_filename);
        
        //生成、保存图像资源
        $output_func = $this->get_output_func();
        $output_func($image,$this->_thumb_path.$sub_path.$filename);
        //关闭资源
        imagedestroy($image);
        imagedestroy($src_image);
        
        //返回压缩图像的地址
        return $sub_path.$filename;
        
    }
}


