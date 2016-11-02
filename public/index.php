<?php
/* 指向public的上一级 */
define("BASE_PATH",  realpath(dirname(__FILE__) . '/../'));

// 定义产品运营环境， production 为生产环境， development 为开发环境， testing 为测试环境
define('ENVIRONMENT', 'development');

date_default_timezone_set('Etc/GMT-8'); // 设置默认时区

require BASE_PATH . '/lib/Wedo/Application.php';

// 异常处理 
// set_exception_handler(array('Wedo\Core\Application', "exceptionHandler"));
$app = new Wedo\Application();
$app->run();
?>
