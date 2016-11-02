<?php
/**
 * 组件公共部份
 * 
 * @author    黄文金
 * @copyright 2014 IGG Inc.
 * @since     Version 1.0
 */
namespace Wedo\Ui;
use Wedo\Logger;

/**
 * 组件公共部份
 */
abstract class AbstractComponent implements ComponentInterface {
    /**
     * 组件解析顺序号
     *
     * @var integer
     */
    protected $index = 0;
    /**
     * 所在的容器组件
     *
     * @var Container
     */
    protected $parent;

    /**
     * 组件属性
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * 表达式变量
     *
     * @var array
     */
    protected $vars = array();

    /**
     * 内容
     *
     * @var string
     */
    protected $content;

    /**
     * 容器内组件
     *
     * @var array
     */
    protected $children = array();

    /**
     * 数据源
     *
     * @var string
     */
    protected $datasource;

    /**
     * name格式
     *
     * @var string
     */
    protected $nameformat;

    /**
     * 浏览模式
     *
     * @var boolean
     */
    protected $viewmode = FALSE;

    /**
     * 构造函数
     *
     * @param string $id   组件ID
     * @param string $name 组件名
     */
    public function __construct($parent = NULL, $index = 0) {        
        $this->index = $index;
        if ($parent) {
            $this->setParent($parent);
        }
    }

    /**
     * 初始化组件属性
     *
     * @param array $attributes 属性数组
     * @return void
     */
    public function initComponentAttributes(array $attributes) {
        if (! $attributes) {
            return;
        }

        foreach ($attributes as $name => $value) {
            // 判断字符串类型
            if (! $value instanceof Expression) {
                $value = Expression::parse($value);
            }

            $vars = get_object_vars($this);
            if (str_contains($name, '-') && array_key_exists(lcfirst(studly_case($name)), $vars)) {
                $name = lcfirst(studly_case($name));
                $method = 'set' . ucfirst($name);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                } else {
                    $this->{$name} = $value;
                }
            } 
            else if (array_key_exists($name, $vars)) {
                $method = 'set' . ucfirst($name);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                } else {
                    $this->{$name} = $value;
                }
            }
            else {
                $this->attributes[$name] = $value;
            }
        }
    }

    /**
     * 设置属性值
     *
     * @param string|array $name  属性名称
     * @param string       $value 属性值        
     * @return void
     */
    public function setAttribute($name, $value = NULL) {
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->setAttribute($key, $val);
            }

            return;
        }

        $this->attributes[$name] = $value;
    }

    /**
     * 取属性
     *
     * @param string $name 属性名称
     * @return string
     */
    public function getAttribute($name) {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : NULL;
    }

    /**
     * 删除属性
     *
     * @param string $name 属性名称
     * @return void
     */
    public function removeAttribute($name) {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
    }

    /**
     * 取属性数组
     *
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * 设置上级组件
     *
     * @param ComponentInterface $parent 上级组件
     * @return void
     */
    public function setParent($parent) {
        if (! $parent) {
            return;
        }

        $this->parent = $parent;
        $this->parent->addChild($this);
    }

    /**
     * 获取上级组件
     *
     * @return ComponentInterface | NULL
     */
    public function getParent($tagName = NULL) {
        if ($tagName) {
            if ($this->parent) {                
                if ($this->parent->getComponentName() == $tagName) {
                    return $this->parent;
                }
                else {
                    return $this->parent->getParent($tagName);
                }
            }
            else {
                return NULL;
            }
        }        

        return $this->parent;
    }

    /**
     * 构造组件属性字符串
     *
     * @param array $attributes  扩展属性
     * @param array $excludeKeys 不包含的KEY
     * @return string
     */
    public static function composeAttributeString(array $attributes, array $excludeKeys = NULL) {
        if ($excludeKeys) {
            $_attrs = array();
            foreach ($attributes as $key => $value) {
                if (!in_array($key, $excludeKeys)) {
                    $_attrs[$key] = $value;
                }
            }
            
            $attributes = $_attrs;
        }

        $str = '';
        foreach ($attributes as $name => $value) {
            $str .= $name . '="' . Expression::getTagExpression($value) .'" ';
        }

        return trim($str);
    }

    /**
     * 在原有属性追加属性值
     *
     * @param array  $attributes 属性数组
     * @param string $name       属性名称
     * @param string $value      属性值
     * @return array
     */
    public static function appendAttributeValue(array $attributes, $name, $value) {
        $src_value = wd_array_val($attributes, $name);
        if ($src_value && is_string($src_value)) {
            $value = $src_value . ' ' . $value;
        }

        $attributes[$name] = $value;
        return $attributes;
    }

    /**
     * 删除原有属性值
     *
     * @param array  $attributes 属性数组
     * @param string $name       属性名称
     * @param string $value      属性值
     * @return array
     */
    public static function removeAttributeValue(array $attributes, $name, $value) {
        $src_value = wd_array_val($attributes, $name);
        
        if ($src_value && (is_string($src_value))) {
            $arr = explode(' ', $src_value);
            $ret = [];
            foreach ($arr as $v) {
                if (trim($v) != $value) {
                    $ret[] = $v;
                }
            }

            $src_value = implode(' ', $ret);
        }

        $attributes[$name] = $src_value;
        return $attributes;
    }

    /**
     * 添加变量表达式
     *
     * @param mixed $expr 表达式
     * @return void
     */
    public function addVar($varname, $expr) {
        $this->vars[$varname] = $expr;
    }
    
    /**
     * 获取变量表达式PHP代码
     *
     * @return string
     */
    public function getEchoExpression() {
        if (! $this->vars) {
            return NULL;
        }
        
        $expr_code = '';
        foreach ($this->vars as $varname => $expr) {
            $code = Expression::getPhpcode($expr, $varname);
            if ($code) {
                $expr_code .= $code . PHP_EOL;
            }            
        }

        return $expr_code;
    }

    /**
     * 魔术方法，设置属性值
     *
     * @param string $name  属性名称
     * @param mixed  $value 属性值
     * @return void
     */
    public function __set($name, $value){ 
        $vars = get_object_vars($this);
        if (array_key_exists($name, $vars)) {
            Logger::debug('AbstractComponent: __set name {}', $name);
            // $name = ucfirst($name);
            // $this->set{$name}($value);
            $this->$name = $value;
        }
        else {
            $this->attributes[$name] = $value;
        }
    } 
    
    /**
     * 魔术方法，取属性值
     *
     * @param string $name 属性名称
     * @return mixed
     */
    public function __get($name){
        if (isset($this->$name)) {
            return $this->$name;
        }
        else {
            return wd_array_val($this->attributes, $name);
        }        
    }

    /**
     * 获取组件名称
     *
     * @return string
     */
    public function getComponentName(){
        $classname = strtolower(get_class($this));
        if ($pos = strrpos($classname, '\\')) {
            return substr($classname, $pos + 1);
        }

        return $pos;
    }

    /**
     * 获取容器内容
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * 添加子组件
     *
     * @param ComponentInterface $component 组件
     * @return void
     */
    public function addChild($component) {
        $this->children[] = $component;
    }

    /**
     * 获取容器内组件
     *
     * @param string $componentName 组件名
     * @return array
     */
    public function getChildren($componentName = NULL) {
        if ($componentName) {
            $ret = array();
            foreach ($this->children as $c) {
                if ($c->getComponentName() == $componentName) {
                    $ret[] = $c;
                }
            }

            return $ret;
        } else {
            return $this->children;
        }
    }

    /**
     * 判断是否有子组件
     *
     * @param string $componentName 组件名
     * @return boolean
     */
    public function hasChildren($componentName = NULL) {
        if (! $this->children) {
            return FALSE;
        }

        if (! $componentName) {
            return TRUE;
        }

        foreach ($this->children as $c) {
            if ($c->getComponentName() == $componentName) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function renderByView() {        
        $view = TagFactory::getInstance()->view();
        if (! $view) {
            return FALSE;
        }

        // 判断是否存在视图模板
        $tpl = 'ui/' . $this->getComponentName();
        Logger::debug('component tpl:{}', $tpl);
        if ($view->exists($tpl)) {
            return $view->render($tpl, array('component' => $this)); 
        }

        return FALSE;
    }

    /**
     * 设置数据源
     *
     * @param string $datasource 数据源表达式或变量名
     * @return string
     */
    public function setDatasource($datasource) {
        $this->datasource = $datasource;
    }

    /**
     * 获取数据源
     *
     * @return string
     */
    public function getDatasource() {
        return $this->datasource;
    }

    /**
     * 设置组件名格式
     *
     * @param string $nameformat 命名格式
     * @return string
     */
    public function setNameformat($nameformat) {
        if ($nameformat instanceof Expression) {
            $this->nameformat = $nameformat->getContent();
        } else {
            $this->nameformat = $nameformat;
        }
    }

    /**
     * 获取组件名格式
     *
     * @return string
     */
    public function getNameformat() {
        return $this->nameformat;
    }

    /**
     * 浏览模式
     *
     * @param mixed $viewmode 浏览模式
     * @return void
     */
    public function setViewmode($viewmode) {
        $this->viewmode = $viewmode;
    }
    
    /**
     * 获取组件名格式
     *
     * @return string
     */
    public function getViewmode() {
        return $this->viewmode;
    }
}
