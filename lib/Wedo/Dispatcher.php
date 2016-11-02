<?php
/**
 * 分发，初始化运行环境
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

use Wedo\View\ViewBlade;
use \Exception;
use Wedo\Exception\ClassNotFoundException;
use Wedo\Exception\CHttpException;
use Wedo\ViewInterface;

/**
 * 分发，初始化运行环境
 */
class Dispatcher {
    /**
     * 路由实例
     *
     * @var \Wedo\Router
     */
    protected $_router;

    /**
     * 视图
     *
     * @var ViewInterface
     */
    protected $_view;

    /**
     * 请求
     *
     * @var Request
     */
    protected $_request;

    /**
     * 单例模式
     *
     * @var Dispatcher
     */
    protected static $_instance;
    /**
     * 是否返回响应内容
     *
     * @var boolean
     */
    protected $_return_response = FALSE;
    /**
     *  instantly flush
     *
     * @var
     */
    protected $_instantly_flush;

    /**
     * 默认模块
     *
     * @var string
     */
    protected $_default_module = 'index';

    /**
     * 默认控制器
     *
     * @var string
     */
    protected $_default_controller = 'index';

    /**
     * 默认action
     *
     * @var string
     */
    protected $_default_action = 'index';

    /**
     * 构造方法
     */
    public function __construct() {
        $this->_router = new Router();
    }

    /**
     * 单例模式
     *
     * @return Dispatcher
     */
    public static function getInstance() {
        if (! self::$_instance) {
            self::$_instance = new Dispatcher();
        }

        return self::$_instance;
    }

    /**
     * 请求分发
     *
     * @param Request $request 请求实例
     * @throws \Wedo\Exception\CHttpException
     * @throws \Exception 
     * @return void
     */
    public function dispatch(Request $request) {
        // 设置默认的控制器、模块、action名称
        $request->setModule($this->_default_module);
        $request->setController($this->_default_controller);
        $request->setAction($this->_default_action);

        $this->_request = $request;
        // 路由器解析
        $this->_router->route($request);
        // 取控制器类
        $controllerClass = $this->getControllerClass($request);
        Logger::debug('controller class : {}', $controllerClass);
        try {
            $controller = new $controllerClass();
        } catch (ClassNotFoundException $e) {
            Logger::debug($e->getMessage());
            throw new CHttpException(404);
        }
        
        if (! $controller instanceof ControllerAbstract) {
            throw new \Exception('控制器必须继承ControllerAbstract类');
        }

        $action = $this->getActionName($request);
        $params = $request->getParams();
        // Logger::debug('controller class: {}, action: {}', $controller_class, $action);
        $controllerAction = array(&$controller, $action);
        if (is_callable($controllerAction)) {
            call_user_func_array($controllerAction, $params);
        } else {
            throw new CHttpException(404);
        }
    }

    /**
     * 取模块路径
     *
     * @param string $module 模块名称
     * @return string
     */
    public function getModulePath($module = NULL) {
        $module = $module ? $module : $this->_default_module;
        return APP_PATH . '/' . ucfirst($module);
    }

    /**
     * 获取当前请求实例
     *
     * @return Request
     */
    public function getRequest() {
        return $this->_request;
    }

    /**
     * 获取路由器
     *
     * @return Router
     */
    public function getRouter() {
        return $this->_router;
    }

    /**
     * 设置视图处理实例
     *
     * @param ViewInterface $view 视图实例
     * @return \Wedo\Dispatcher
     */
    public function setView(ViewInterface $view) {
        $this->_view = $view;
        return $this;
    }

    /**
     * 获取视图
     *
     * @return ViewInterface
     */
    public function getView() {
        // 默认使用 \Wedo\View\ViewBlade 视图模板
        $this->_view OR $this->_view = new ViewBlade();
        return $this->_view;
    }
    
    /**
     * 设置默认模块名称
     *
     * @param string $module 模块名称 
     * @return \Wedo\Dispatcher
     */
    public function setDefaultModule($module) {
        $this->_default_module = $module;
        return $this;
    }

    /**
     * 获取默认模块名称
     *
     * @return string
     */
    public function getDefaultModule() {
        return $this->_default_module;
    }

    /**
     * 设置默认控制器名称
     *
     * @param string $controller 控制器名称 
     * @return \Wedo\Dispatcher
     */
    public function setDefaultController($controller) {
        $this->_default_controller = $controller;
        return $this;
    }

    /**
     * 获取默认控制器名称
     *
     * @return string
     */
    public function getDefaultController() {
        return $this->_default_controller;
    }

    /**
     * 获取Action名称
     *
     * @param Request $request 请求实例
     * @return string
     */
    public function getControllerClass(Request $request) {
        $controller_class = wd_print("{}\\Controllers\\{}Controller", ucfirst($request->getModule()), wd_studly($request->getController()));
        if (! $prefix = $this->getApplication()->getAppsNsPrefix()) {
            $app_directory = $this->getApplication()->getAppDirectory();
            $prefix = ucfirst(basename($app_directory)) . '\\';
        }

        $controller_class = $prefix . $controller_class;
        return $controller_class;
    }

    /**
     * 获取Action名称
     *
     * @param Request $request 请求实例
     * @return string
     */
    public function getActionName(Request $request) {
         return lcfirst($request->getAction()) . 'Action';
//        return strtolower($request->getMethod()) . ucfirst($request->getAction());
    }

    /**
     * 设置默认Action
     *
     * @param string $action Action名称
     * @return \Wedo\Dispatcher
     */
    public function setDefaultAction($action) {
        $this->_default_action = $action;
        return $this;
    }

    /**
     * 获取默认Action
     *
     * @return string
     */
    public function getDefaultAction() {
        return $this->_default_action;
    }

    /**
     * 禁用视图
     *
     * @return \Wedo\Dispatcher
     */
//    public function disableView() {
//        $this->autoRender(FALSE);
//        return $this;
//    }

    /**
     * 启用视图
     *
     * @return \Wedo\Dispatcher
     */
//    public function enableView() {
//        $this->autoRender(TRUE);
//        return $this;
//    }

    /**
     * 获取应用实例
     *
     * @return \Wedo\Application
     */
    public function getApplication() {
        return Application::app();
    }
}