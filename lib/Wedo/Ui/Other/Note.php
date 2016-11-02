<?php
/**
 * 文本输入组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Other;

use Wedo\Ui\AbstractComponent;

class Note extends AbstractComponent {
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'help-block m-b-none');
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        $code = '<p ' . $attributeHtml . '>' . $this->getContent() . '</p>' . PHP_EOL;
        return $code;
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

        $this->content = $content;
        
        return $this->render();
    }
}