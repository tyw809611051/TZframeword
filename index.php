<?php
require './framework/core/Framework.class.php';
use framework\core\Framework;

/* 定义常量 */
//根目录常量
define('ROOT', str_replace('\\', '/', __DIR__).'/');
//平台目录常量
define('APP_PATH', ROOT.'application/');
//框架目录常量
define('FRAME_PATH', ROOT.'framework/');
//静态文件路径常量
define('PUBLIC_PATH', '/project/1221/application/public/');
//上传文件路径常量
define('UPLOAD_PATH', './application/public/uploads');
//压缩文件路径常量
define('THUMB_PATH', './application/public/static/');
// 定义字体文件路径
define('FONT_PATH', APP_PATH.'public/fonts/');

new Framework();