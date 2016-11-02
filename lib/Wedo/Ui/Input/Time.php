<?php
/**
 * 时间输入组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

use Wedo\Ui\TagFactory;

/**
 * Time 组件
 */ 
class Time extends Text {
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        parent::init();
    }

    /**
     * 获取编辑模式代码
     *
     * @return string
     */
    public function getEditCode() {
        TagFactory::getInstance()->loadCss("timepicker", assets("css/plugins/clockpicker/clockpicker.css"));
        TagFactory::getInstance()->loadJs("timepicker", assets('js/plugins/clockpicker/clockpicker.js'));

        $option = "{placement: 'top', donetext: 'Done'}";

        $script = "\$('.time-picker').clockpicker({$option});";
        TagFactory::getInstance()->loadJscript('timepicker', $script);
        
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'time-picker');
        $this->attributes['data-autoclose'] = 'true';
        return parent::getEditCode();
    }
}