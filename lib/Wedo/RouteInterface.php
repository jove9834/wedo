<?php
/**
 * 路由规则接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

/**
 * 路由规则接口
 */
interface RouteInterface {
    /**
     *  将路由规则组合成一个url
     *
     * @param array $info  URL信息
     * @param array $query 查询参数
     * @return string
     */
    public function assemble(array $info, array $query = array());

    /**
     * 路由
     *
     * @param Request $request 请求实例
     * @return boolean
     */
    public function route(Request $request);
}