<?php
/**
 * Cell组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Grid;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\AbstractComponent;
use Wedo\Ui\Layout\DefaultLayout;
use Wedo\Ui\Expression;

/**
 * Cell组件
 */
class Cell extends AbstractComponent {
    /**
     * 组件标题
     *
     * @var string
     */
    protected $label;
    /**
     * 复选框列
     *
     * @var string
     */
    protected $checkbox;

    /**
     * 绑定数据源列，即数组中的KEY
     *
     * @var string
     */
    protected $name;
    /**
     * 隐藏样式，为屏幕自适应属性，取值为：xs,sm,md,lg;
     *
     * @var string
     */
    protected $hide;

    /**
     * 宽度，占比
     *
     * @var integer
     */
    protected $width = 1;

    /**
     * 表达式
     *
     * @var Expression
     */
    protected $expression;
    
    /**
     * 设置复选框
     *
     * @param mixed $value 值
     * @return void
     */
    public function setCheckbox($value) {
        $this->checkbox = $value;
    }

    /**
     * 获取复选框
     *
     * @return void
     */
    public function getCheckbox() {
        return $this->checkbox;
    }

    /**
     * 设置隐藏
     *
     * @param mixed $value 值
     * @return void
     */
    public function setHide($value) {
        $this->hide = $value;
    }

    /**
     * 获取隐藏
     *
     * @return void
     */
    public function getHide() {
        return $this->hide;
    }

    /**
     * 设置宽度
     *
     * @param mixed $value 值
     * @return void
     */
    public function setWidth($value) {
        if ($value instanceof Expression) {
            $this->width = (int)$value->__toString();
        }
        else {
            $this->width = (int)$value;
        }
    }

    /**
     * 获取宽度
     *
     * @return void
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * 设置表达式
     *
     * @param mixed $value 值
     * @return void
     */
    public function setExpression($value) {
        $this->expression = $value;
    }

    /**
     * 获取表达式
     *
     * @return void
     */
    public function getExpression() {
        return $this->expression;
    }

    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() { 
        parent::init();
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        return NULL;
    }

    public function getHeader($totalWidth) {
        $attributes = $this->getAttributes();
        $percent = intval(100 * $this->getWidth() / $totalWidth);
        $attributes['width'] = $percent . '%';
        $attributeHtml = self::composeAttributeString($attributes);
        $code = '<th ' . $attributeHtml . '>';
        if ($this->checkbox) {            
            $code .= '<input type="checkbox" class="i-checks">';
        }
        else {
            $code .= $this->label;
        }

        $code .= '</th>' . PHP_EOL;
        return $code;
    }

    public function getBody($item_var = 'row') {
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        $code = '<td ' . $attributeHtml . '>' . PHP_EOL;

        if ($this->checkbox) {            
            $code .= '<input type="checkbox" class="i-checks" value="<?php echo wd_array_val($' . $item_var . ', \'' . $this->name . '\');?>">';
        }
        else {
            if ($this->content) {
                $code .= $this->content;
            }
            else {
                $code .= '<?php echo wd_array_val($' . $item_var . ', "' . $this->name . '");?>';
            }
        }

        $code .= '</td>' . PHP_EOL;
        return $code;
    }

    public function getBodyByTable($item_val = 'el') {
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        $code = '<td ' . $attributeHtml . '>' . PHP_EOL;

        if ($this->content) {
            $code .= $this->content;
        }
        else {
            $code .= '@{{' . $item_val . '.' . $this->name . '}}';
        }
        
        $code .= '</td>' . PHP_EOL;
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
        
        $this->content = $content;
        
        return $this->render();
    }

}