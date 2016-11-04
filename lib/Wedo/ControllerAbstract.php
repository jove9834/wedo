<?php
/**
 * 控制器基类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

use Wedo\View\ViewBlade;

/**
 * 控制器基类
 */
class ControllerAbstract
{
    /**
     * 视图路径
     *
     * @var array
     */
    protected $_view_path = array();

    /**
     * 模板变量
     *
     * @return array
     */
    protected $_tpl_vars = array();

    /**
     * 为视图引擎分配一个模板变量
     *
     * @param string $name  变量名称
     * @param mixed  $value 变量值
     */
    public function assign($name, $value = NULL) {
        if (! $name) {
            return;
        }

        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->assign($key, $val);
            }
        } else {
            $name = trim($name);
            $this->_tpl_vars[$name] = $value;
        }
    }

    /**
     * Display
     *
     * @param string $tpl      视图模板文件(可选)
     * @param array  $tpl_vars 视图变量(可选)
     * @return void
     */
    protected function display($tpl = NULL, array $tpl_vars = NULL) {
        echo $this->render($tpl, $tpl_vars);
    }

    /**
     * 宣染视图
     *
     * @param string $tpl      视图模板文件(可选)
     * @param array  $tpl_vars 视图变量(可选)
     * @return string
     */
    protected function render($tpl = NULL, array $tpl_vars = NULL) {
        $module_view_path = Dispatcher::instance()->getModulePath($this->getModule()) . '/views';

        $this->setViewPath($module_view_path);
        $_view = $this->getView();
        if (! $_view) {
            $_view = new ViewBlade();
            Dispatcher::instance()->setView($_view);
        }

        $_view->addLocation($this->_view_path);

        if (! $tpl) {
            $tpl = lcfirst($this->getRequest()->getController()) . '.' . lcfirst($this->getRequest()->getAction());
        }

        $vars = $this->_tpl_vars;
        if ($tpl_vars) {
            $vars = array_merge($vars, $tpl_vars);
        }

        Logger::debug(json_encode($this->_view_path));

        return $this->getView()->render($tpl, $vars);        
    }

    /**
     * 获取模块名称
     *
     * @return string
     */
    public function getModule() {
        return $this->getRequest()->getModule();
    }

    /**
     * 获取Request对象
     *
     * @return Request
     */
    public function getRequest() {
        return Dispatcher::instance()->getRequest();
    }

    /**
     * 获取视图对象
     *
     * @return ViewInterface
     */
    public function getView() {
        return Dispatcher::instance()->getView();
    }

    /**
     * 设置视图路径
     *
     * @param string $path 视图路径
     * @return void
     */
    public function setViewPath($path) {
        if (! $path || in_array($path, $this->_view_path)) {
            return;
        }

        array_unshift($this->_view_path, $path);
    }

    /**
     * 跳转URL
     *
     * @param string $url URL地址
     * @return void
     */
    public function redirect($url) {
        Logger::debug('redirect url {}', $url);
        header("Location: " . $url, TRUE, 302);
        exit();
    }

    /**
     * 跳转URL
     *
     * @param string $module     模块名称
     * @param string $controller 控制器名称
     * @param string $action     action
     * @param array  $query      查询参数
     * @return void
     */
    public function forward($module, $controller = NULL, $action = NULL, array $query = NULL) {
        $info = array(':m' => $module, ':c' => $controller, ':a' => $action);
        $uri = Dispatcher::instance()->getRouter()->getCurrentRoute()->assemble($info, $query);
        $request = $this->getRequest();
        $request->setRequestUri($uri);
        Dispatcher::instance()->dispatch($request);
    }
}
