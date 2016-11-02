<?php
/**
 * Ibody 组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Panel;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\Container;

/**
 * Ibody 组件
 */ 
class Ibody extends Container {
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
        $attributes = $this->getAttributes(); // self::appendAttributeValue($this->getAttributes(), 'class', 'ibox-content');
        $attributeHtml = self::composeAttributeString($attributes);
        $code = '<div ' . $attributeHtml . '>' . PHP_EOL;    
        $code .= '<div class="widget-body">' . PHP_EOL;
        $code .= $this->getContent() . PHP_EOL;
        $code .= '</div></div>' . PHP_EOL;
        return $code;
    }

}