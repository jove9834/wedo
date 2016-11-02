<?php
/**
 * Action组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui;

/**
 * Action组件
 */
abstract class Action extends AbstractComponent {
    /**
     * 按钮标题
     *
     * @var string
     */
    protected $title;
    
    /**
     * 构造函数
     *
     * @param mixed   $parent 父组件
     * @param integer $index  组件序号
     */
    public function __construct($parent = NULL, $index = 0) {
        $this->setParent($parent);
        $this->index = $index;        
    }

    /**
     * 解析标签组件
     *
     * @param array  $attributes 标签属性
     * @param string $content    标签内容，包含在标签内的内容
     * @return string
     */
    public function make(array $attributes = NULL, $content = NULL) {
        // 初始化组件属性
        $this->initComponentAttributes($attributes);

        // 组件初始化
        $this->init();

        $this->content = TagFactory::getInstance()->make($content, $this);

        return $this->render();
    }

    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() { }
    /**
     * 设置标题
     *
     * @param string $title 标题
     * @return void
     */    
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * 获取标题
     *
     * @return string
     */ 
    public function getTitle() {
        return $this->title;
    }
}