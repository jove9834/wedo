<?php
/**
 * 容器组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Button;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\Action;
use Wedo\Ui\Container;
use Wedo\Ui\Expression;

/**
 * Button 组件
 */ 
class Button extends Action {
    /**
     * 按钮大小
     *
     * @var string
     */
    protected $size = 'nm';

    /**
     * 按钮样式
     *
     * @var string
     */
    protected $skin = 'default';

    /**
     * 按钮类型, 取值为 button, link, submit
     *
     * @var string
     */
    protected $type = 'link';

    /**
     * 设置按钮类型
     *
     * @param string $type 按钮类型
     * @return void
     */    
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * 获取按钮类型
     *
     * @return string
     */ 
    public function getType() {
        return $this->type;
    }

    /**
     * 设置按钮样式
     *
     * @param string $skin 按钮样式,
     * @return void
     */    
    public function setSkin($skin) {
        $this->skin = $skin;
    }

    /**
     * 获取按钮样式
     *
     * @return string
     */ 
    public function getSkin() {
        return $this->skin;
    }

    /**
     * 设置按钮大小
     *
     * @param string $size 按钮大小
     * @return void
     */    
    public function setSize($size) {
        $this->size = $size;
    }

    /**
     * 获取按钮大小
     *
     * @return string
     */ 
    public function getSize() {
        return $this->size;
    }

    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        $class = 'btn ';
        switch ($this->size) {
            case 'lg':
                $class .= 'btn-lg ';
                break;
            case 'sm':
                $class .= 'btn-sm ';
                break;
            case 'xs':
                $class .= 'btn-xs ';
                break;
            default:
                break;
        }

        switch ($this->skin) {
            case 'primary':
                $class .= 'btn-primary ';
                break;
            case 'info':
                $class .= 'btn-info ';
                break;
            case 'warning':
                $class .= 'btn-warning ';
                break;
            case 'danger':
                $class .= 'btn-danger ';
                break;
            case 'warning':
                $class .= 'btn-warning ';
                break;
            case 'white':
                $class .= 'btn-white ';
                break;
            default:
                $class .= 'btn-default ';
                break;
        }

        $_class = $this->getAttribute('class');
        if ($_class) {
            $class .= $_class;
        }

        $this->setAttribute('class', $class);
    }

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $title = Expression::getPhpcode($this->title);

        if (! $title) {
            $title = $this->content;
        }

        if ($this->type == 'menuitem') {
            $attributeHtml = self::composeAttributeString($this->getAttributes(), array('class'));
            return '<a ' . $attributeHtml . '>' . $title . '</a>' . PHP_EOL;
        }

        $attributeHtml = self::composeAttributeString($this->getAttributes());
        if ($this->type == 'link') {
            $code = '<a ' . $attributeHtml . '>' . $title . '</a>' . PHP_EOL;  
        } else {
            $code = '<button ' . $attributeHtml . ' type="' . $this->type . '">' . $title . '</button>' . PHP_EOL;  
        }

        if ($this->getParent() && $this->getParent() instanceof Container && $this->getParent()->getLayoutInstance()) {
            $code = $this->getParent()->getLayoutInstance()->render($this, $code);
        }

        return $code;
    }


}