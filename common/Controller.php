<?php
/**
 * 控制器基类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common;

use Wedo\ControllerAbstract;
use Wedo\Dispatcher;

/**
 * 控制器基类
 */
class Controller extends ControllerAbstract {
    /**
     * 构造函数
     */
    public function __construct() {
        if (! defined('TEMPLATE')) {
            define('TEMPLATE', 'clear');
            define('THEME', 'default');
        }
        
        // 添加框架模板目录
        $frame_view_path = PUBLIC_PATH . '/templates/' . TEMPLATE;
        $this->setViewPath($frame_view_path);  
    }

    /**
     * Ajax请求入口
     *
     * @return void
     */
    public function ajaxAction() {
        // Dispatcher::getInstance()->disableView();
        $response = new AjaxResponse();
        $cmd = wd_input('cmd');
        if (! $cmd) {
            // 出错
            $response->addError('没有cmd参数');
            $response->send();
            return;
        }
        
        $response->setDefaultCmd($cmd);

        $method = 'ajax' . studly_case($cmd);
        call_user_func(array($this, $method), $response);
        $response->send();
    }
}
