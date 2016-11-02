<?php
/**
 * Ibox 组件
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
 * Ibox 组件
 */ 
class Ibox extends Container {
    /**
     * 是否允许收缩
     *
     * @var boolean
     */
    protected $collapsed = FALSE;

    /**
     * 是否允许全屏
     *
     * @var boolean
     */
    protected $fullscreen = FALSE;

    /**
     * 是否关闭
     *
     * @var boolean
     */
    protected $closed = FALSE;

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
        $attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'jarviswidget');
        $attributes['data-widget-collapsed'] = $this->collapsed;
        $attributes['data-widget-fullscreenbutton'] = $this->fullscreen;
        $attributes['data-widget-deletebutton'] = $this->closed;        
        $attributeHtml = self::composeAttributeString($attributes);
        
        $code = '<div ' . $attributeHtml . '>' . PHP_EOL;
        $header = $this->getHeader();
        $body = $this->getBody();
        if ($header) {
            $code .= $header->render();
        }

        if ($body) {
            $code .= $body->render();
        }

        $code .= '</div>' . PHP_EOL;

        return $code;
    }

    /**
     * 获取iheader组件
     *
     * @return Container
     */
    protected function getHeader() {
        $children = $this->getChildren('iheader');
        if ($children) {
            return $children[0];
        }

        return FALSE;
    }

    /**
     * 获取ibody组件
     *
     * @return Container
     */
    protected function getBody() {
        $children = $this->getChildren('ibody');
        if ($children) {
            return $children[0];
        }

        return FALSE;
    }

}