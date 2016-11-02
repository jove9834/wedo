<?php
/**
 * 垂直排列的表单
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Layout;

use Wedo\Ui\LayoutInterface;
use Wedo\Ui\Container;
use Wedo\Ui\Other\InputGroup;
use Wedo\Ui\Input;
use Wedo\Ui\Input\Hidden;
use Wedo\Ui\Expression;

/**
 * 垂直排列的表单
 */
class FormVerticalLayout implements LayoutInterface {

    /**
     * 宣染组件
     *
     * @param mixed  $component     组件
     * @param string $componentHtml 组件HTML
     * @return string
     */
    public function render($component, $componentHtml){
        if ($component instanceof Button || $component instanceof ButtonGroup || $component instanceof DropdownMenu) {
            return $this->renderButton($component, $componentHtml);
        }

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
        
        $span = (int)$component->span;
        $title = Expression::getPhpcode($component->title);
        $id = $component->id;        
        $code = '<div class="col-sm-' . $span .'">' . PHP_EOL;
        $code .= '<label for="' . $id . '">';        

        if ($component->required) {
            $code .= '<font color="red">*</font> ';
        }

        $code .= $title;
        
        $code .= '</label>' . PHP_EOL;     
        $code .= $componentHtml . PHP_EOL;
        $code .= '</div>' . PHP_EOL;

        if ($span == 12 || $span == 0) {
            $code = '<div class="form-group">' . $code . '</div>' . PHP_EOL;
        }

        return $code;
    }

    /**
     * 宣染Button
     *
     * @param mixed  $component     组件
     * @param string $componentHtml 组件HTML
     * @return string
     */
    protected function renderButton($component, $componentHtml) {
        $code = '<div class="form-group"><div class="col-sm-12">' . $componentHtml . '</div></div>' . PHP_EOL;
        
        return $code;
    }

}