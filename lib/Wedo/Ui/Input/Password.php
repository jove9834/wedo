<?php
/**
 * 密码输入组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

/**
 * 密码输入组件
 */
class Password extends Text {
    /**
     * 类型，text, hidden, password
     *
     * @var string
     */
    protected $type = 'password';

    /**
     * 获取浏览模式代码
     *
     * @return string
     */
    public function getViewCode() {
        $code = $this->composeInputCode('********', TRUE);        
        return $this->getViewCodeByStyle($code);
    }
}