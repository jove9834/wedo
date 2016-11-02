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
class RadioGroup extends CheckboxGroup {
    /**
     * 获取浏览模式代码
     *
     * @return string
     */
    public function getViewCode() {
        $code = '';
        if (is_array($this->options)) {
            $this->addVar('$_options', $this->options);    
            $code = '<?php echo wd_array_val($_options, ' . Expression::getExpressionString($this->getValue()) . '); ?>' . PHP_EOL;
        }
        else if ($this->options instanceof Expression) {
            $code = '<?php echo wd_array_val(' . $this->options->getContent() . ', ' . Expression::getExpressionString($this->getValue()) . '); ?>' . PHP_EOL;
        }
        
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
        $code = $this->renderOptionForEdit($this->options);

        $code = $this->composeInputCode($code);
        if ($this->layout != 'none') {
            $code = '<div class="rdo-group">' . $code . '</div>' . PHP_EOL;
        }

        return $code;
    }

    /**
     * 宣染编辑下的下拉选项
     *
     * @param mixed $options 选项
     * @return string
     */
    protected function renderOptionForEdit($options) {
        $code = '';
        if (! $options) {
            return $code;
        }

        $id = $this->id ?: 'radio';        
        $attributes = $this->getAttributes();
        $attributeHtml = self::composeAttributeString($attributes, array('id'));
        
        if ($options instanceof Expression) {   
            $code .= '<?php foreach (' . $options->getContent() . ' as $key => $value): ?>' . PHP_EOL;
            if ($this->layout == 'vertical') {
                $code .= '<div class="radio">' . PHP_EOL;
                $code .= '<label><input type="radio" id="' . $id . '<?php echo $key;?>" value="<?php echo $key;?>" ' . $attributeHtml .' <?php echo strcmp($key, '. Expression::getExpressionString($this->getValue()) .') == 0 ? "checked":"";?>><?php echo $value;?></label>' . PHP_EOL;
                $code .= '</div>' . PHP_EOL;
            } else if ($this->layout == 'horizontal') {
                $code .= '<label class="radio-inline"><input type="radio" id="' . $id . '<?php echo $key;?>" value="<?php echo $key;?>" ' . $attributeHtml .' <?php echo strcmp($key, '. Expression::getExpressionString($this->getValue()) .') == 0 ? "checked":"";?>><?php echo $value;?></label>' . PHP_EOL;
            } else {
                $code .= '<input type="radio" id="' . $id . '<?php echo $key;?>" value="<?php echo $key;?>" ' . $attributeHtml .' <?php echo strcmp($key, '. Expression::getExpressionString($this->getValue()) .') == 0 ? "checked":"";?>><?php echo $value;?>' . PHP_EOL;
            }
            
            $code .= "<?php endforeach; ?>" . PHP_EOL;      
        } 
        else if (is_array($options)) {
            foreach ($options as $key => $value) {
                if ($this->layout == 'vertical') {
                    $code .= '<div class="radio">' . PHP_EOL;
                    $code .= '<label><input type="radio" id="' . $id . $key . '" value="' . $key . '" ' . $attributeHtml .' <?php echo strcmp("' . $key . '", '. Expression::getExpressionString($this->getValue()) .') == 0 ? "checked":"";?>>' . $value . '</label>' . PHP_EOL;
                    $code .= '</div>' . PHP_EOL;
                } else if ($this->layout == 'horizontal') {
                    $code .= '<label class="radio-inline"><input type="radio" id="' . $id . $key . '" value="' . $key . '" ' . $attributeHtml .' <?php echo strcmp("' . $key . '", '. Expression::getExpressionString($this->getValue()) .') == 0 ? "checked":"";?>>' . $value . '</label>' . PHP_EOL;
                } else {
                    $code .= '<input type="radio" id="' . $id . $key . '" value="' . $key . '" ' . $attributeHtml .' <?php echo strcmp("' . $key . '", '. Expression::getExpressionString($this->getValue()) .') == 0 ? "checked":"";?>>' . $value . PHP_EOL;
                }
            }
        }

        return $code;
    }
}