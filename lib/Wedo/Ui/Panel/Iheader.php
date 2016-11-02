<?php
/**
 * Iheader 组件
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
 * Iheader 组件
 */ 
class Iheader extends Container {
    /**
     * 组件标题
     *
     * @var string
     */
    protected $title;
    /**
     * ICON图标
     *
     * @var string
     */
    protected $icon;

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
        $attributes = $this->getAttributes(); // self::appendAttributeValue($this->getAttributes(), 'class', 'ibox-title');
        $attributeHtml = self::composeAttributeString($attributes);
       
        $code = '<header ' . $attributeHtml . '>' . PHP_EOL;
        if ($this->icon) {
            $code .= '<span class="widget-icon"> <i class="' . $this->icon . '"></i> </span>' . PHP_EOL;
        }

        if ($this->title) {
            $code .= '<h2>' . $this->title . '</h2>' . PHP_EOL;
        }

        $code .= '<div class="widget-toolbar">' . PHP_EOL;

        $code .= $this->getContent() . PHP_EOL;        

        $code .= '</div></header>' . PHP_EOL;
        
        return $code;
    }

}