<?php
/**
 * 组件接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui;

/**
 * 组件接口
 */
interface ComponentInterface {
    /**
     * 宣染组件
     *
     * @return string
     */
    public function render();

    /**
     * 解析标签组件
     *
     * @param array  $attributes 标签属性
     * @param string $content    标签内容，包含在标签内的内容
     * @return string
     */
    public function make(array $attributes = NULL, $content = NULL);

    /**
     * 添加属性
     *
     * @param string|array $name  属性名称
     * @param string       $value 属性值        
     * @return void
     */
    public function setAttribute($name, $value = NULL);

    /**
     * 取属性
     *
     * @param string $name 属性名称
     * @return string
     */
    public function getAttribute($name);

    /**
     * 取属性数组
     *
     * @return array
     */
    public function getAttributes();
    
    /**
     * 设置上级组件
     *
     * @param ComponentInterface $parent 上级组件
     * @return void
     */
    public function setParent($parent);

    /**
     * 获取上级组件
     *
     * @return ComponentInterface | NULL
     */
    public function getParent();
}