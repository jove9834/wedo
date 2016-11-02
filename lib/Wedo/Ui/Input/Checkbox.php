<?php
/**
 * 复选框组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

use Wedo\Ui\Expression;
use Wedo\Ui\Input;

class Checkbox extends Input {
    /**
     * 布局,取值：horizontal(水平), vertical(垂直), none
     *
     * @var boolean
     */
    protected $layout = 'horizontal';

    /**
     * checkbox value属性
     *
     * @var string
     */
    protected $val;

    /**
     * checkbox label属性
     *
     * @var string
     */
    protected $label;
    
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        parent::init();
        $this->attributes = self::removeAttributeValue($this->getAttributes(), 'class', 'form-control');
    }    
    
    /**
     * 获取浏览模式代码
     *
     * @return string
     */
    public function getViewCode() {
        $value = Expression::getExpressionString($this->getValue());
        $val = Expression::getExpressionString($this->val);

        $code = '<?php if (strcmp(' . $val . ', ' . $value . ') == 0):?>' . PHP_EOL;
        $code .= Expression::getPhpcode($this->label);
        $code .= '<?php endif;?>' . PHP_EOL;

        $code = $this->composeInputCode($code, TRUE);
        $code = $this->getViewCodeByStyle($code);
        
        return $code;
    }

    /**
     * 获取编辑模式代码
     *
     * @return string
     */
    public function getEditCode() {
        $html = '';        
        $id = $this->id ?: 'chk';        
        $value = Expression::getExpressionString($this->getValue());
        $val = Expression::getExpressionString($this->val);
        $attributes = $this->getAttributes();
        $attributeHtml = self::composeAttributeString($attributes);
        
        if ($this->layout == 'vertical') {
            $html .= '<div class="checkbox">' . PHP_EOL;
            $html .= '<label><input type="checkbox" id="' . $id . '" value="' . Expression::getPhpcode($this->val) . '" ' . $attributeHtml .' <?php echo strcmp(' . $val . ', '. $value .') == 0 ? "checked":"";?>>' . Expression::getPhpcode($this->label) . '</label>' . PHP_EOL;
            $html .= '</div>' . PHP_EOL;
        } else if ($this->layout == 'horizontal') {
            $html .= '<label class="checkbox-inline"><input type="checkbox" id="' . $id . '" value="' . Expression::getPhpcode($this->val) . '" ' . $attributeHtml .' <?php echo strcmp(' . $val . ', '. $value .') == 0 ? "checked":"";?>>' . Expression::getPhpcode($this->label) . '</label>' . PHP_EOL;
        } else {
            $html .= '<input type="checkbox" id="' . $id . '" value="' . Expression::getPhpcode($this->val) . '" ' . $attributeHtml .' <?php echo strcmp(' . $val . ', '. $value .') == 0 ? "checked":"";?>>' . Expression::getPhpcode($this->label) . PHP_EOL;
        }
        
        $code = $this->composeInputCode($html);
        
        return $code;
    }
}