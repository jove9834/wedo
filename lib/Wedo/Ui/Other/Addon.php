<?php
/**
 * 文本输入组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Other;

use Wedo\Ui\AbstractComponent;
use Wedo\Ui\Expression;

class Addon extends AbstractComponent {
    /**
     * 位置
     *
     * 取值：after, before
     *
     * @var string
     */
    protected $place = 'after';

    /**
     * 设置位置
     *
     * @param string $place 位置取值为after, before
     * @return void
     */
    public function setPlace($place) {
        if ($place instanceof Expression) {
            $this->place = $place->getContent();
        } else {
            $this->place = $place;    
        }
    }

    /**
     * 获取位置
     *
     * @return string
     */
    public function getPlace() {
        return $this->place;
    }
    
    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $code = '<span class="input-group-addon">' . $this->getContent() . '</span>' . PHP_EOL;
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
        
        return NULL;
    }
}