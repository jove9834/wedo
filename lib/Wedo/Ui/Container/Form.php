<?php
/**
 * 容器组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Container;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\Container;
use Wedo\Ui\Layout\DefaultLayout;

class Form extends Container {
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() { 
        parent::init();
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', $this->layout);
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $attributeHtml = self::composeAttributeString($this->getAttributes(), array('datasource', 'viewmode', 'nameformat'));
        $code = '<form ' . $attributeHtml . '>' . PHP_EOL;
        $code .= $this->content . PHP_EOL;
        $code .= '</form>' . PHP_EOL;

        return $code;
    }

}