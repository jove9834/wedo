<?php
/**
 * 单选框组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

use Wedo\Ui\Expression;

/**
 * 单选框组件
 */
class Radio extends Checkbox {
    
    /**
     * 获取编辑模式代码
     *
     * @return string
     */
    public function getEditCode() {
        $html = '';        
        $id = $this->id ?: 'radio';        
        $value = Expression::getExpressionString($this->getValue());
        $val = Expression::getExpressionString($this->val);
        $attributes = $this->getAttributes();
        $attributeHtml = self::composeAttributeString($attributes);
        
        if ($this->layout == 'vertical') {
            $html .= '<div class="radio">' . PHP_EOL;
            $html .= '<label><input type="radio" id="' . $id . '" value="' . Expression::getPhpcode($this->val) . '" ' . $attributeHtml .' <?php echo strcmp(' . $val . ', '. $value .') == 0 ? "checked":"";?>>' . Expression::getPhpcode($this->label) . '</label>' . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
        } else if ($this->layout == 'horizontal') {
            $html .= '<label class="radio-inline"><input type="radio" id="' . $id . '" value="' . Expression::getPhpcode($this->val) . '" ' . $attributeHtml .' <?php echo strcmp(' . $val . ', '. $value .') == 0 ? "checked":"";?>>' . Expression::getPhpcode($this->label) . '</label>' . PHP_EOL;
        } else {
            $html .= '<input type="radio" id="' . $id . '" value="' . Expression::getPhpcode($this->val) . '" ' . $attributeHtml .' <?php echo strcmp(' . $val . ', '. $value .') == 0 ? "checked":"";?>>' . Expression::getPhpcode($this->label) . PHP_EOL;
        }
        
        $code = $this->composeInputCode($html);
        
        return $code;
    }
}