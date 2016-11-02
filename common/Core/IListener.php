<?php
/**
 * 监听接口类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Core;

interface IListener {
    /**
     * 监听处理
     *
     * @param string $event      事件
     * @param array  $parameters 参数
     * @return void
     */
    public function handle($event, array $parameters = NULL);
}