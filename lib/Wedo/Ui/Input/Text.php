<?php
/**
 * 文本输入组件
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
 * 文本输入组件
 */
class Text extends Input {
    /**
     * 类型，text, hidden, password
     *
     * @var string
     */
    protected $type = 'text';

    /**
     * 最大长度
     *
     * @var integer
     */
    protected $maxlength = 0;

    /**
     * 只能是数字或小数点
     *
     * @var boolean
     */
    protected $number = FALSE;

    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        parent::init();
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'form-control');
        if ($this->maxlength > 0) {
            $this->setAttribute('data-dismiss', 'validate');
            $this->setAttribute('data-maxlength', $this->maxlength);
        }

        if ($this->number) {
            $this->setAttribute('data-dismiss', 'validate');
            $this->setAttribute('data-number', 'true');
        }
    }

    /**
     * 获取浏览模式代码
     *
     * @return string
     */
    public function getViewCode() {
        $code = Expression::getPhpcode($this->getValue());
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
        $attributes['value'] = $this->getValue();
        $attributes['type'] = $this->type;
        $attributeHtml = self::composeAttributeString($attributes);
        $code = '<input ' . $attributeHtml . '>';        
        return $this->composeInputCode($code);
    }
}