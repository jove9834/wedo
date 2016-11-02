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

/**
 * 复选框组件
 */
class CheckboxGroup extends Select {
    /**
     * 布局,取值：horizontal(水平), vertical(垂直), none
     *
     * @var boolean
     */
    protected $layout = 'horizontal';
    
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
        $code = '';
        $value = $this->getValue();        

        if ($this->preOptions) {
            if ($this->preOptions->getType() == Expression::TYPE_ARRAY || $this->preOptions->getType() == Expression::TYPE_JSON) {
                $this->preOptions->setVarname('$_pre_options');
                $this->addVar($this->preOptions);                                
            }
        }

        if ($this->options instanceof Expression) {
            if ($this->options->getType() == Expression::TYPE_ARRAY || $this->options->getType() == Expression::TYPE_JSON) {
                $this->options->setVarname('$_options');
                $this->addVar($this->options);                
            }

            if ($this->preOptions) {
                $code = '<?php echo wd_options_text(' . $this->options . ', wd_explode(' . $value . '), ' . $this->preOptions . '); ?>' . PHP_EOL;
            }
            else {
                $code = '<?php echo wd_options_text(' . $this->options . ', wd_explode(' . $value . ')); ?>' . PHP_EOL;
            }
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
        if (! $this->options) {
            return '<div class="chk-group"></div>' . PHP_EOL;   
        }

        // 复选框名称加上[]
        $name = $this->getAttribute('name');
        $name .= '[]';
        $this->setAttribute('name', $name);

        $code = $this->renderOptionForEdit($this->preOptions);
        $code .= $this->renderOptionForEdit($this->options);

        $code = $this->composeInputCode($code);
        if ($this->layout != 'none') {
            $code = '<div class="chk-group">' . $code . '</div>' . PHP_EOL;
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
        $html = '';
        if (! $options) {
            return $html;
        }

        $id = $this->id ?: 'chk';        
        $val = Expression::getExpressionString($this->getValue());
        $attributes = $this->getAttributes();
        $attributeHtml = self::composeAttributeString($attributes);
        
        if ($options instanceof Expression) {
            $html .= '<?php foreach (' . $options->getContent() . ' as $key => $value): ?>' . PHP_EOL;
            if ($this->layout == 'vertical') {
                $html .= '<div class="checkbox">' . PHP_EOL;
                $html .= '<label><input type="checkbox" id="' . $id . '<?php echo $key;?>" value="<?php echo $key;?>" ' . $attributeHtml .' <?php echo wd_in_stringlist($key, '. $val .') ? "checked":"";?>><?php echo $value;?></label>' . PHP_EOL;
                $html .= '</div>' . PHP_EOL;
            } else if ($this->layout == 'horizontal') {
                $html .= '<label class="checkbox-inline"><input type="checkbox" id="' . $id . '<?php echo $key;?>" value="<?php echo $key;?>" ' . $attributeHtml .' <?php echo wd_in_stringlist($key, '. $val .') ? "checked":"";?>><?php echo $value;?></label>' . PHP_EOL;                    
            } else {
                $html .= '<input type="checkbox" id="' . $id . '<?php echo $key;?>" value="<?php echo $key;?>" ' . $attributeHtml .' <?php echo wd_in_stringlist($key, '. $val .') ? "checked":"";?>><?php echo $value;?>' . PHP_EOL;                    
            }

            $html .= "<?php endforeach; ?>" . PHP_EOL;        
        } 
        else if (is_array($options)) {
            foreach ($options as $key => $value) {
                if ($this->layout == 'vertical') {
                    $html .= '<div class="checkbox">' . PHP_EOL;
                    $html .= '<label><input type="checkbox" id="' . $id . $key . '" value="' . $key . '" ' . $attributeHtml .' <?php echo wd_in_stringlist("' . $key . '", '. $val .') ? "checked":"";?>>' . $value . '</label>' . PHP_EOL;
                    $html .= '</div>' . PHP_EOL;
                } else if ($this->layout == 'horizontal') {
                    $html .= '<label class="checkbox-inline"><input type="checkbox" id="' . $id . $key . '" value="' . $key . '" ' . $attributeHtml .' <?php echo wd_in_stringlist("' . $key . '", '. $val .') ? "checked":"";?>>' . $value . '</label>' . PHP_EOL;
                } else {
                    $html .= '<input type="checkbox" id="' . $id . $key . '" value="' . $key . '" ' . $attributeHtml .' <?php echo wd_in_stringlist("' . $key . '", '. $val .') ? "checked":"";?>>' . $value . PHP_EOL;
                }
            }
        }

        return $html;
    }
}