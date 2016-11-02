<?php
/**
 * 模块测试类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Apps\Sys\Test;

use Wedo\TestCase;
use Wedo\Logger;
use Common\Models\LogModel;
/**
 * 模块测试类
 */
class LogTest extends TestCase {
    /**
     * 标题
     *
     * @var string
     */
    protected $suiteTitle = "操作日志测试用例";
    
    public function testingAdd() {
        //writeLog($log_type, $operate_type, $description, $module = FALSE, $operator = FALSE)
        $log_type = 'login';
        $operate_type = 'login';
        $description = '登录系统';
        $module = 'index';
        LogModel::instance()->writeLog($log_type, $operate_type, $description);
        $this->assertTrue(TRUE);
    }
}