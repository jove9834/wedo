<?php
/**
 * 默认路由规则
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Route;

use Wedo\RouteInterface;
use Wedo\Request;
use Wedo\Application;
use Wedo\Dispatcher;

/**
 * 默认路由规则
 */
class RouteStatic implements RouteInterface {
    /**
     *  指定路由规则组合成一个url
     *
     * @param array $info  URL信息
     * @param array $query 查询参数
     * @return string
     */
    public function assemble(array $info, array $query = NULL) {
        $module = wd_array_val($info, ':m', Dispatcher::getInstance()->getDefaultModule());
        $controller = wd_array_val($info, ':c', Dispatcher::getInstance()->getDefaultController());
        $action = wd_array_val($info, ':a', Dispatcher::getInstance()->getDefaultAction());
        $params = wd_array_val($info, ':p');

        $url = '/' . $module . '/' . $controller  . '/' . $action;
        if ($params && is_array($params)) {
            $url .= '/' . implode('/', $params);
        }

        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    /**
     * 路由
     *
     * @param Request $request 请求实例
     * @return boolean
     */
    public function route(Request $request) {
        $requestUri = $request->getRequestUri();
        if (! $requestUri || $requestUri == '/') {
            // 默认路由
            return TRUE;
        }

        $segments = explode('/', $requestUri);
        $modules = Application::app()->getModules();
        if (count($segments) > 2) {
            if (in_array(ucfirst($segments[0]), $modules)) {
                $request->setModule($segments[0]);
                $request->setController($segments[1]);
                $request->setAction($segments[2]);
                // 参数
                if (count($segments) > 3) {
                    $request->setParams(array_slice($segments, 3));
                }
            } else {
                // 默认的模块index
                $request->setController($segments[0]);
                $request->setAction($segments[1]);
                $request->setParams(array_slice($segments, 2));
            }
        } elseif (count($segments) == 2) {
            if (in_array(ucfirst($segments[0]), $modules)) {
                $request->setModule($segments[0]);
                $request->setController($segments[1]);
            } else {
                $request->setController($segments[0]);
                $request->setAction($segments[1]);
            }
        } else {
            $request->setController($segments[0]);
        }

        return TRUE;
    }
}