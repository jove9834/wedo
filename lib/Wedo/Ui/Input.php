<?php
/**
 * 输入组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui;

/**
 * 输入组件
 */
abstract class Input extends AbstractComponent {     
    /**
     * 组件值
     *
     * @var string
     */
    protected $value;

    /**
     * 绑定字段
     *
     * @var string
     */
    protected $bind;

    /**
     * 组件标题
     *
     * @var string
     */
    protected $title;

    /**
     * 组件占用列，一行为12列，默认一个组件占用一行即12列
     *
     * @var integer
     */
    protected $span = 12;

    /**
     * 是否必填
     *
     * @var boolean
     */
    protected $required = FALSE;

    /**
     * view模式下的样式, 默认为static, 可选值如none
     *
     * @var string
     */
    protected $viewStyle = 'none';

    /**
     * view模式下是否有ID属性
     *
     * @var boolean
     */
    protected $viewHasId = FALSE;

    /**
     * 构造函数
     *
     * @param mixed   $parent 父组件
     * @param integer $index  组件序号
     */
    public function __construct($parent = NULL, $index = 0) {        
        $this->index = $index;
        if (! $parent) {
            return;
        }
            
        $this->setParent($parent);
        if ($parent->datasource) {
            $this->setDatasource($parent->datasource);        
        }

        if ($parent->viewmode) {
            $this->setViewmode($parent->viewmode);
        }

        if ($parent->nameformat) {
            $this->setNameformat($parent->nameformat);
        }
    }
    
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() { 
        if (! ($name = $this->getAttribute('name'))) {
            $name = $this->bind;
        }

        if (! $this->getAttribute('id') && $this->bind) {
            $this->id = $this->bind;
        }

        if ($name) {
            if ($nameformat = $this->getNameformat()) {
                if (!str_contains($nameformat, '[') || !str_contains($name, '[')) {
                    $name = str_replace("*", $name, $nameformat);
                }                
            }

            $this->setAttribute('name', $name);
        }

        if ($this->required) {
            $this->setAttribute('data-dismiss', 'validate');
            $this->setAttribute('data-required', 'true');
        }
    }

    /**
     * 设置组件值
     *
     * @param mixed $value 值
     * @return void
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * 获取组件ID
     *
     * @return void
     */
    public function getValue() {
        if ($this->bind && $this->datasource) {
            if ($this->value) {
                return new Expression(Expression::TYPE_EXPRESSION, "wd_array_val(" . $this->datasource . ", '" . $this->bind . "', " .  Expression::getExpressionString($this->value) . ")");
            }
            else {
                return new Expression(Expression::TYPE_EXPRESSION, "wd_array_val(" . $this->datasource . ", '" . $this->bind . "')");
            }
            
        }
        else if ($this->value) {
            return $this->value;
        }
        else {
            return "";
        }
    }

    /**
     * 设置绑定字段
     *
     * @param string $bind 值
     * @return void
     */
    public function setBind($bind) {
        $this->bind = $bind;   
        $this->setAttribute('data-bind', $bind);         
    }

    /**
     * 获取绑定的字段
     *
     * @return string
     */
    public function getBind() {
        return $this->bind;
    }
    
    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $html = '';        

        if ($this->viewmode instanceof Expression) {
            $html .= "<?php if (" . $this->viewmode . "): ?>" . PHP_EOL;
            $html .= $this->getViewCode() . PHP_EOL;
            $html .= "<?php else: ?>" . PHP_EOL;
            $html .= $this->getEditCode() . PHP_EOL;
            $html .= "<?php endif; ?>" . PHP_EOL;
        } 
        else if ($this->viewmode) {
            $html .= $this->getViewCode() . PHP_EOL;
        } 
        else {
            $html .= $this->getEditCode() . PHP_EOL;
        }

        // 输出变量
        $vars_str = $this->getEchoExpression();        

        $code = $vars_str . PHP_EOL . $html;

        if ($this->getParent() && $this->getParent() instanceof Container && $this->getParent()->getLayoutInstance()) {
            $code = $this->getParent()->getLayoutInstance()->render($this, $code);
        }
        
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
        
        // 组件初始化
        $this->init();
        $this->content = TagFactory::getInstance()->make($content, $this);
        
        $code = $this->renderByView();
        if ($code === FALSE) {
            $code = $this->render();            
        }

        return $code;
    }

    /**
     * 获取浏览模式代码
     *
     * @return string
     */
    abstract public function getViewCode();

    /**
     * 获取编辑模式代码
     *
     * @return string
     */
    abstract public function getEditCode();

    /**
     * 输入组合组件代码
     *
     * @param string  $inputCode 输入组件代码
     * @param boolean $viewmode  是否浏览
     * @return string
     */
    protected function composeInputCode($inputCode, $viewmode = FALSE) {
        $code = $inputCode;
        $class = $viewmode ? 'wd-input-group' : 'input-group';
        if ($this->hasChildren('addon')) {
            $code = '<div class="' . $class . '">' . PHP_EOL;
            $before_code = $after_code = '';
            foreach ($this->getChildren('addon') as $c) {
                if ($c->getPlace() == 'before') {
                    $before_code .= $c->render();
                } else {
                    $after_code .= $c->render();
                }
            }

            $code .= $before_code . $inputCode . $after_code;
            $code .= '</div>' . PHP_EOL;
        }

        $code .= $this->getContent();

        return $code;
    }

    /**
     * 根据设置的样式获取Viewmode状态下的HTML代码
     *
     * @param string $code HTML代码
     * @return string
     */
    protected function getViewCodeByStyle($code) {
        if ($this->viewStyle == 'none') {
            return $code;
        }

        $attributeHtml = '';
        if ($this->viewHasId) {
            $keys = array_keys($this->getAttributes());        
            $keys = array_diff($keys, array('id', 'name', 'class'));
            $attributes = $this->getAttributes();
            $attributes = self::removeAttributeValue($attributes, 'class', 'form-control');
            $attributes = self::appendAttributeValue($attributes, 'class', 'form-control-static');
            $attributeHtml = self::composeAttributeString($attributes, $keys);    
        }

        if ($this->viewStyle == 'static') {
            $code = '<p ' . $attributeHtml . '>' . $code .'</p>';    
        }

        return $code;
    }

}