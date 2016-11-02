<?php
/**
 * 组件布局接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui;

interface LayoutInterface {

    /**
     * 宣染组件
     *
     * @param mixed  $component     组件
     * @param string $componentHtml 组件HTML
     * @return string
     */
    public function render($component, $componentHtml);

}