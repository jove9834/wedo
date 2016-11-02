<?php
/**
 * Tab 组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Panel;

use Wedo\Ui\Expression;
use Wedo\Ui\Container;
use Wedo\Ui\TagFactory;

/**
 * Tab 组件
 */ 
class Tab extends Container {
    /**
     * ID
     *
     * @var string
     */
    protected $id;

    /**
     * 标题
     *
     * @var string
     */
    protected $title;

    /**
     * 是否激活
     *
     * @var boolean
     */
    protected $active = FALSE;

    /**
     * icon图标
     *
     * @var string
     */
    protected $icon;

    /**
     * URL
     *
     * @var string
     */
    protected $url;

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
        // 返回替代符
        return $this->getReplaceTag();
    }

    /**
     * 获取tab组件
     *
     * @param string $tabId Tab组件ID
     * @return string
     */
    public function getTabCode($tabId) {
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        
        if (! $this->id) {
            $this->id = $tabId;
        }

        if ($this->active instanceof Expression) {
            $script = '<?php if (' . Expression::getExpressionString($this->active) . '):?>' . PHP_EOL;
            $script .= '$("#' . $this->id . ' > a").tab("show")' . PHP_EOL;
            $script .= '<?php endif;?>' . PHP_EOL;
            TagFactory::getInstance()->loadJscript('tab-' . $this->id, $script);
        }
        else if($this->active) {
            $script = '$("#' . $this->id . ' > a").tab("show")';
            TagFactory::getInstance()->loadJscript('tab-' . $this->id, $script);
        }

        return wd_print('<li id="{}" {}><a data-toggle="tab" href="#{}_c">{}</a></li>', $this->id, $attributeHtml, Expression::getPhpcode($this->id), Expression::getPhpcode($this->title));
    }

    /**
     * 获取tab组件
     *
     * @param string $tabId Tab组件ID
     * @return string
     */
    public function getTabContentCode($tabId) {
        if (! $this->id) {
            $this->id = $tabId;
        }

        $attributes = $this->getAttributes();
        $attributes = self::appendAttributeValue($attributes, 'class', 'tab-pane');
        $attributeHtml = self::composeAttributeString($attributes);

        $code = wd_print('<div id="{}_c" {}>', Expression::getPhpcode($this->id), $attributeHtml);
        $code .= '<div class="panel-body">';
        $code .= $this->getContent();
        $code .= '</div></div>';
        return $code;
    }

    /**
     * 获取替换标记
     *
     * @return string
     */
    public function getReplaceTag() {
        return wd_print('<!--tab{}-->', $this->index);
    }
}