<?php
/**
 * 隐藏域组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

/**
 * 隐藏域组件
 */
class Hidden extends Text {
    /**
     * 类型，text, hidden, password
     *
     * @var string
     */
    protected $type = 'hidden';

    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        parent::init();
    }

    /**
     * 获取浏览模式代码
     *
     * @return string
     */
    public function getViewCode() {
        return NULL;
    }
}