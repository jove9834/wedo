<?php
/**
 * DropdownMenu组件
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
 * DropdownMenu组件
 */
class DropdownMenu extends AbstractComponent {
    /**
     * 按钮大小
     *
     * @var string
     */
    protected $size = 'nm';

    /**
     * 按钮样式
     *
     * @var string
     */
    protected $skin = 'white';

    /**
     * 标题
     *
     * @var string
     */
    protected $title;


    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $code = '<div class="btn-group">' . PHP_EOL;
        $code .= '<button data-toggle="dropdown" class="' . $this->getDropdownClass() . '">' . $this->title . ' <span class="caret"></span></button>';
        $code .= '<ul class="dropdown-menu">';

        $children = $this->getChildren('button');
        if ($children) {
            foreach ($children as $btn) {
                $btn->type = 'menuitem';
                $code .= '<li>' . $btn->render() . '</li>' . PHP_EOL;
            }
        }

        $code .= '</ul></div>' . PHP_EOL;
        return $code;
    }

    /**
     * 获取下拉样式类
     *
     * @return string
     */
    private function getDropdownClass() {
        $class = 'btn btn-' . $this->skin . ' ';
        switch ($this->size) {
            case 'lg':
                $class .= 'btn-lg';
                break;
            case 'sm':
                $class .= 'btn-sm';
                break;
            case 'xs':
                $class .= 'btn-xs';
                break;
            default:
                break;
        }

        return $class .' dropdown-toggle';
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