<?php
/**
 * 默认的布局
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Layout;

use Wedo\Ui\LayoutInterface;

class DefaultLayout implements LayoutInterface {

    /**
     * 宣染组件
     *
     * @param mixed  $component     组件
     * @param string $componentHtml 组件HTML
     * @return string
     */
    public function render($component, $componentHtml) {
        return $componentHtml;
    }

}