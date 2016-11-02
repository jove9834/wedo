<?php
/**
 * ButtonGroup组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Button;

use Wedo\Ui\AbstractComponent;
use Wedo\Ui\TagFactory;

/**
 * ButtonGroup组件
 */
class ButtonGroup extends AbstractComponent {
    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $code = '<div class="btn-group">' . PHP_EOL;

        $children = $this->getChildren('button');
        if ($children) {
            foreach ($children as $btn) {
                $btn->type = 'link';
                $code .= $btn->render() . PHP_EOL;
            }
        }

        $code .= '</div>' . PHP_EOL;
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

        $this->content = TagFactory::getInstance()->make($content, $this);

        return $this->render();
    }
}