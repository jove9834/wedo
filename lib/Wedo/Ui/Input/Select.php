<?php
/**
 * 下拉输入组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

use Wedo\Ui\Input;
use Wedo\Ui\Expression;

/**
 * 下拉输入组件
 */ 
class Select extends Input {
    /**
     * 下拉选项
     *
     * @var string
     */
    protected $options;

    /**
     * 插入选项
     *
     * @var string
     */
    protected $preOptions;

    /**
     * 字典名称
     *
     * @var string
     */
    protected $dict;

    /**
     * 是否使用Label作为Value,用于数据源为一维数组
     *
     * @var boolean
     */
    protected $useLabel = FALSE;
    
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        parent::init();
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'form-control');
    }
    
    /**
     * 设置字典名称
     *
     * @param string $dict 字典名称
     * @return void
     */
    public function setDict($dict) {
        $this->dict = $dict;
        if (! $dict instanceof Expression) {
            $this->options = Expression::parse("{{EIP\Common\Sys\Dict::loadDicData('".$dict."')}}");    
        }
        else {
            $this->options = Expression::parse("{{EIP\Common\Sys\Dict::loadDicData(".$dict.")}}");    
        }
    }

    /**
     * 获取浏览模式代码
     *
     * @return string
     */
    public function getViewCode() {
        $code = '';
        $_pre_options = NULL;
        $_options = NULL;
        
        if ($this->preOptions) {
            if (is_array($this->preOptions)) {
                $_pre_options = '$_pre_options';
                $this->addVar($_pre_options, $this->preOptions);
            }
            else if ($this->preOptions instanceof Expression) {
                $_pre_options = $this->preOptions->getContent();      
            }
        }

        if (is_array($this->options)) {
            $_options = '$_options';
            $this->addVar($_options, $this->options);
        } elseif ($this->options instanceof Expression) {
            $_options = $this->options->getContent();      
        }

        if ($this->preOptions) {
            $code = '<?php echo wd_array_val(' . $_pre_options . ', ' . Expression::getExpressionString($this->getValue()) . ') == FALSE ? wd_array_val(' . $_options . ', ' . Expression::getExpressionString($this->getValue()) . ') : wd_array_val(' . $_pre_options . ', ' . Expression::getExpressionString($this->getValue()) . '); ?>' . PHP_EOL;
        }
        else {
            $code = '<?php echo wd_array_val(' . $_options . ', ' . Expression::getExpressionString($this->getValue()) . '); ?>' . PHP_EOL;
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
        $attributes = $this->getAttributes();
        $attributeHtml = self::composeAttributeString($attributes);
        $code = '<select ' . $attributeHtml . '>' . PHP_EOL;        
        $code .= $this->renderOptionForEdit($this->preOptions);
        $code .= $this->renderOptionForEdit($this->options);
        $code .= '</select>' . PHP_EOL;

        return $this->composeInputCode($code);
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
        
        if ($options instanceof Expression) {            
            $code .= '<?php foreach (' . $options->getContent() . ' as $key => $value): ?>' . PHP_EOL;
            if ($this->useLabel) {
                $code .= '<option value="<?php echo $value;?>" <?php echo strcmp($value, '. Expression::getExpressionString($this->getValue()) .') == 0 ? "selected":"";?>><?php echo $value;?></option>' . PHP_EOL;
            }
            else {
                $code .= '<option value="<?php echo $key;?>" <?php echo strcmp($key, '. Expression::getExpressionString($this->getValue()) .') == 0 ? "selected":"";?>><?php echo $value;?></option>' . PHP_EOL;    
            }
            
            $code .= "<?php endforeach; ?>" . PHP_EOL;            
        } 
        else if (is_array($options)) {
            foreach ($options as $key => $value) {
                if ($this->useLabel) {
                    $code .= '<option value="' . $value . '" <?php echo strcmp("' . $value . '", '. Expression::getExpressionString($this->getValue()) .') == 0 ? "selected":"";?>>' . $value . '</option>' . PHP_EOL;
                }
                else {
                    $code .= '<option value="' . $key . '" <?php echo strcmp("' . $key . '", '. Expression::getExpressionString($this->getValue()) .') == 0 ? "selected":"";?>>' . $value . '</option>' . PHP_EOL;    
                }
            }
        }

        return $code;
    }
}