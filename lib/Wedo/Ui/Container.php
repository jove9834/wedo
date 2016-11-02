<?php
/**
 * 容器组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui;

use Wedo\Ui\Layout\DefaultLayout;
use Wedo\Ui\Layout\FormHorizontalLayout;
use Wedo\Ui\Layout\FormVerticalLayout;
use Wedo\Ui\Layout\FormInlineLayout;
/**
 * 容器组件
 */
abstract class Container extends AbstractComponent { 
    /**
     * 容器内组件布局
     *
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * 布局实例
     *
     * @var LayoutInterface
     */
    protected $layoutInstance;

    /**
     * 构造函数
     *
     * @param mixed   $parent 父组件
     * @param integer $index  组件序号
     */
    public function __construct($parent = NULL, $index = 0) {
        $this->index = $index;
        if ($parent) {
            $this->setParent($parent);
            if ($parent->datasource) {
                $this->setDatasource($parent->datasource);        
            }

            if ($parent->viewmode) {
                $this->setViewmode($parent->viewmode);
            }

            if ($parent->nameformat) {
                $this->setNameformat($parent->nameformat);
            }

            if ($parent->layout) {
                $this->setLayout($parent->layout);
            }
        }  
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

        // $this->content_d = $content;

        $this->content = TagFactory::getInstance()->make($content, $this);
        $code = $this->renderByView();
        if ($code === FALSE) {
            $code = $this->render();            
        }        

        if ($this->getParent() && $this->getParent()->getLayoutInstance()) {
            $code = $this->getParent()->getLayoutInstance()->render($this, $code);
        }
        
        return $code;
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    abstract public function render();

    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {

        if ($this->layout == 'form-horizontal') {
            $this->layoutInstance = new FormHorizontalLayout();
        }
        else if ($this->layout == 'form-vertical') {
            $this->layoutInstance = new FormVerticalLayout();
        }
        else if ($this->layout == 'form-inline') {
            $this->layoutInstance = new FormInlineLayout();
        }
        else {
            $this->layoutInstance = new DefaultLayout();
        }
    }

    /**
     * 设置组件布局
     *
     * @param string $layout 布局
     * @return void
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * 获取组件布局
     *
     * @return string
     */
    public function getLayout() {
        return $this->layout;
    }

    public function getLayoutInstance() {
        return $this->layoutInstance;
    }
}