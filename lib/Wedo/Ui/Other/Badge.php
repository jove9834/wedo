<?php
/**
 * Badges, Labels组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Other;

use Wedo\Ui\AbstractComponent;

/**
 * Badges, Labels组件
 */
class Badge extends AbstractComponent {
    const BADGE = 'badge';
    const LABEL = 'label';
    /**
     * 类型 badge, label
     *
     * @var string
     */
    protected $type = 'badge';

    /**
     * 类型 plain primary info success warning danger
     *
     * @var string
     */
    protected $skin = 'plain';

    /**
     * 宣染组件
     *
     * @return string
     */
    public function render() {
        $class = $this->getSkinClass();
        $attributes = self::appendAttributeValue($this->getAttributes(), 'class', $class);
        $attributeHtml = self::composeAttributeString($attributes);

        $code = '<span ' . $attributeHtml . '>' . $this->getContent() . '</span>' . PHP_EOL;
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
    
    /**
     * 获取样式类
     *
     * @return string
     */
    protected function getSkinClass() {
        $class = '';
        if ($this->type == self::BADGE) {
            $class = 'badge badge';
        }
        else {
            $class = 'label label';
        }

        return $class . '-' . $this->skin;
    }
}