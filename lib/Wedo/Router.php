<?php
/**
 * 路由器
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

use Wedo\Route\RouteStatic;

/**
 * 路由器
 */
class Router {
    /**
     * 路由数组
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * 当前路由数组
     *
     * @var array
     */
    protected $_current;

    /**
     * 添加路由规则
     *
     * @param string $name        路由规则名称
     * @param Wedo\RouteInterface $route 路由规则
     * @return Wedo\Router
     */
    public function addRoute($name, RouteInterface $route) {
        $this->_routes[$name] = $route;
        return $this;
    }

    /**
     * 获取当前路由规则
     *
     * @return Wedo\RouteInterface
     */
    public function getCurrentRoute() {
        return $this->_current;
    }

    /**
     * 根据路由规则名称取路由规则实例
     *
     * @return Wedo\RouteInterface 当不存在，则返回FALSE
     */
    public function getRoute($name) {
        return wd_array_val($this->_routes, $name);
    }

    /**
     * 获取路由规则数组
     *
     * @return array
     */
    public function getRoutes() {
        return $this->_routes;
    }

    /**
     * 路由
     *
     * @param Request $request 请求对象
     * @return void
     */
    public function route(Request $request) {
        foreach ($this->_routes as $route) {
            if ($route->route($request)) {
                $this->_current = $route;
                return;
            }
        }

        // 执行默认的路由规则
        $this->_current = new RouteStatic();
        $this->_current->route($request);
    }
}