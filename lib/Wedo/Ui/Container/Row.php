<?php
/**
 * Row 组件
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
 * Row 组件
 */ 
class Row extends Container {
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() { 
        parent::init();
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'form-group');
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        $code = '<div ' . $attributeHtml . '>' . PHP_EOL;
        $code .= $this->getContent() . PHP_EOL;
        $code .= '</div>' . PHP_EOL;

        return $code;
    }

}