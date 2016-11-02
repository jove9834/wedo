<?php
/**
 * Modal 组件
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
 * Modal 组件
 */ 
class Modal extends Container {
    /**
     * 组件ID
     *
     * @var string
     */
    protected $id;

    /**
     * 组件初始化设置
     *
     * @return void
     */  
    public function init() {
        parent::init();
        if (! $this->id) {
            $this->id = 'tabpanel' . $this->index;
        }

        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'tabs-container');
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        $code = '<div id="' . $this->id . '" ' . $attributeHtml . '>' . PHP_EOL;
        $tabs = $this->getTabs();
        $nav = $this->content;        
        $content = $this->content;
        
        foreach ($tabs as $index => $tab) {
            $tabId = $this->id . '_' . $index;
            $tabNavCode = $tab->getTabCode($tabId);
            $tabContentCode = $tab->getTabContentCode($tabId);
            $nav = str_replace($tab->getReplaceTag(), $tabNavCode, $nav); 
            $content = str_replace($tab->getReplaceTag(), $tabContentCode, $content); 
        }

        $code .= '<ul class="nav nav-tabs">' . $nav . '</ul>' . PHP_EOL;
        $code .= '<div class="tab-content">' . $content . '</div>' . PHP_EOL;
        $code .= '</div>' . PHP_EOL;

        return $code;
    }

    /**
     * 获取tab组件
     *
     * @return Container
     */
    protected function getTabs() {
        return $this->getChildren('tab');
    }
}