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

use Wedo\Ui\Expression;

/**
 * 文本输入组件
 */ 
class Textarea extends Text {
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        parent::init();
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'autosize');
    }
    
    /**
     * 获取编辑模式代码
     *
     * @return string
     */
    public function getEditCode() {
        $attributes = $this->getAttributes();
        $attributeHtml = self::composeAttributeString($attributes);

        $code = '<textarea ' . $attributeHtml . '>';
        $code .= Expression::getPhpcode($this->getValue());
        $code .= '</textarea>';
        return $this->composeInputCode($code);
    }
}