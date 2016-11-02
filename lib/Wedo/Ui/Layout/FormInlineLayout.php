<?php
/**
 * Inline排列的表单
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Layout;

use Wedo\Ui\LayoutInterface;
use Wedo\Ui\Container;
use Wedo\Ui\Container\InputGroup;
use Wedo\Ui\Input;
use Wedo\Ui\Input\Hidden;
use Wedo\Ui\Expression;

/**
 * Inline排列的表单
 */
class FormInlineLayout implements LayoutInterface {

    /**
     * 宣染组件
     *
     * @param mixed  $component     组件
     * @param string $componentHtml 组件HTML
     * @return string
     */
    public function render($component, $componentHtml) {
        if ($component instanceof Input || $component instanceof InputGroup) {
            return $this->renderInput($component, $componentHtml);
        }

        return $componentHtml . PHP_EOL;    
    }

    /**
     * 宣染输入组件
     *
     * @param mixed  $component     组件
     * @param string $componentHtml 组件HTML
     * @return string
     */
    protected function renderInput($component, $componentHtml) {
        if ($component instanceof Hidden) {
            return $componentHtml;
        }

        $title = Expression::getPhpcode($component->title);
        $id = $component->id;
        $code = '<label for="' . $id . '" class="sr-only">' . $title . '</label>';
        $code .= $componentHtml;        
        $code = '<div class="form-group">' . $code . '</div>' . PHP_EOL;

        return $code;
    }
}