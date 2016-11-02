<?php
/**
 * 树型组件中的TreeItem组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Tree;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\AbstractComponent;
use Wedo\Ui\Layout\DefaultLayout;
use Wedo\Ui\Expression;

/**
 * 树型组件中的TreeItem组件
 */
class TreeItem extends AbstractComponent {
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() { 
        parent::init();
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        return NULL;
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
        
        $this->content = $content;
        
        return $this->render();
    }

}