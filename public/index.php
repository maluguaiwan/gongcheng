<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
// 定义URL
if (!defined('WEB_URL')) {
    $url = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
    define('WEB_URL', (('/' == $url || '\\' == $url) ? '' : $url));
}
if(file_exists("install") && !file_exists("install.lock")){
    // 组装安装url
    $url = $_SERVER['HTTP_HOST'].trim($_SERVER['SCRIPT_NAME'],'index.php').'install/index.php';
    // 使用http://域名方式访问；避免./public/install 路径方式的兼容性和其他出错问题
    header("Location:http://$url");die;
}
// 定义应用目录
define('APP_PATH',dirname(__DIR__) . '/application/');
define('ROOT_PATH',__DIR__ . '/');
define('DS','/');
define('SITE_URL','http://'.$_SERVER['HTTP_HOST']); //

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
