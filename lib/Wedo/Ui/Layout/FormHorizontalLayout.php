<?php
/**
 * 水平排列的表单
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
use Wedo\Ui\Button\Button;
use Wedo\Ui\Button\ButtonGroup;
use Wedo\Ui\Button\DropdownMenu;

/**
 * 水平排列的表单
 */
class FormHorizontalLayout implements LayoutInterface {

    /**
     * 宣染组件
     *
     * @param mixed  $component     组件
     * @param string $componentHtml 组件HTML
     * @return string
     */
    public function render($component, $componentHtml) {
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
        $span = $span - 2;
        $title = Expression::getPhpcode($component->title);
        $id = $component->id;
        $code = '<label for="' . $id . '" class="col-sm-2 control-label">';
        
        if ($component->required) {
            $code .= '<font color="red">*</font> ';
        }
        $code .= $title;

        $code .= '</label>';
        $code .= '<div class="col-sm-' . $span . '">';    
        $code .= $componentHtml . PHP_EOL;        
        $code .= '</div>' . PHP_EOL;

        if ($component->span == 12 || $component->span == 0) {
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
        $code = '<div class="form-group"><div class="col-lg-offset-2 col-lg-10">' . $componentHtml . '</div></div>' . PHP_EOL;
        
        return $code;
    }


}