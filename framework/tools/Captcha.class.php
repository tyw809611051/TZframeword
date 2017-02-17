<?php
namespace framework\tools;
/*
 * 验证码类
 */
class Captcha
{
    //成员属性
    private $_width = 100;  //画布默认宽度
    private $_height = 20;  //画布默认的高度
    private $_font = 15;    //验证码字体大小
    private $_number = 4;   //默认显示4个字符
    
    public function __set($p,$v)
    {
    	if(property_exists($this, $p)){
    		$this->$p = $v;
    	}
    }
    //成员方法
    //生成一张图像，并输出到浏览器
    public function makeImage()
    {
        //1. 先创建一个画布(在内存中创建一个图像资源)
        $image = imagecreatetruecolor($this->_width,$this->_height);
        
        //2. 给画布填充颜色，  allocate分配
        $color = imagecolorallocate($image, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
        imagefill($image, 0, 0, $color);
        
        //创建随机的文字
        $code = $this -> makeCode();
        //将随机的字符输出到图像资源中
        //让字符串居中显示（思路：画布的宽高-字符的宽高）/2
        //通过imagefontwidth()获得一个字符的宽度
        $src_w = imagefontwidth($this->_font);
        //imagefontheight(font)获得在font这个字体下一个字符的高度
        $src_h = imagefontheight($this->_font);
        //四个字符的宽度
        $str_len = $src_w * $this -> _number;
        //因为就一行，所以高度就是一个字符的高度
        $x = ($this->_width - $str_len)/2-10;
        $y = ($this-> _height - $src_h)/2;
        
        //字体的颜色
        $color = imagecolorallocate($image, mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
        
        //imagestring($image, $this->_font, $x, $y, $code, $color);
              
        for($i=0;$i<$this -> _number;$i++){
            imagettftext($image, 20, mt_rand(0,45), $x+$i*20, $y+20, $color, 
            		FONT_PATH.'STXINWEI.TTF', $code[$i]);
        }
        
        //添加100个干扰像素点
        for($i=0;$i<100;$i++){
            //生成随机的颜色
            $color = imagecolorallocate($image, mt_rand(100,255),mt_rand(100,255),mt_rand(100,255));
            //绘制像素点
            imagesetpixel($image, mt_rand(0,$this->_width), mt_rand(0,$this->_height), $color);
        }
        //添加10条干扰线条
        for($i=0;$i<5;$i++){
            $color = imagecolorallocate($image, mt_rand(150,250),mt_rand(150,250),mt_rand(150,250));
            imageline($image, mt_rand(0,$this->_width), mt_rand(0,$this->_height), mt_rand(0,$this->_width), mt_rand(0,$this->_height), $color);
        }
        
        //4. 直接在浏览器输出这个画布
        header("Content-Type:image/png");
        //生成图像,如果增加第二个参数表示保存到本地
        imagepng($image);
        //5. 销毁内存中图像资源
        imagedestroy($image);
    }
    //产生随机文字的函数
    private function makeCode()
    {
        //随机的文字可能是数字、字母
        //range()会产生一个从a到z的字符的集合（数组）
        $upper_str = range('A','Z');
        $lower_str = range('a','z');
        $num = range(1,9);
        
        //把上面三个数组合并
        $data = array_merge($upper_str,$lower_str,$num);
        //为了让产生的数字更随机,先打乱一下顺序
        shuffle($data);
        
        //从上面数组中随机取出4个
        $randoms = array_rand($data,4);
        
        //通过下标获得对应的字符
        $str = '';
        foreach ($randoms as $v){
            $str .= $data[$v];
        }  
        //将生成的随机的字符，保存起来，便于将来在其他地方使用
        //session_start();
        $_SESSION['captcha_code'] = $str;
        return $str;
    }
    //验证用户输入的验证码和我们生成是否一致
    //$code是调用函数时传递进来的，将传递的验证码和生成的session中的比较(不区分大小写)
    public function checkCode($code)
    {
        //session_start();
        $result = strtoupper($code) == strtoupper($_SESSION['captcha_code']);
        if($result){
            return true;
        }else{
            return false;
        }
    }
}
