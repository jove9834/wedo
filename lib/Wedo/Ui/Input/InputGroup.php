<?php
/**
 * 输入组件组
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

use Wedo\Ui\Input;

/**
 * InputGroup 组件,将多个组件并排
 */ 
class InputGroup extends Input {
    /**
     * 获取浏览模式代码
     *
     * @return string
     */
    public function getViewCode() {

        return $this->getContent();
    }

    /**
     * 获取编辑模式代码
     *
     * @return string
     */
    public function getEditCode() {
        return '<div class="wd-input-group">' . $this->getContent() . '</div>';
    }

}