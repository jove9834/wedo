<?php
/**
 * FieldSet 组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Container;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\Container;

/**
 * FieldSet 组件
 */ 
class FieldSet extends Container {
    /**
     * 组件标题
     *
     * @var string
     */
    protected $title;

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

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        $code = '<fieldset ' . $attributeHtml . '>' . PHP_EOL;
        $code .= '<legend>' . $this->getTitle() . '</legend>' . PHP_EOL;        
        $code .= $this->content . PHP_EOL;
        $code .= '</fieldset>' . PHP_EOL;
        
        return $code;
    }

}