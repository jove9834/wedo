<?php
/**
 * 日期输入组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

use Wedo\Ui\TagFactory;

/**
 * Date 组件
 */ 
class Date extends Text {
    /**
     * 组件初始化设置
     *
     * @return void
     */    
    public function init() {
        parent::init();
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'date-picker');
        $this->setAttribute('data-dismiss', 'validate');
        $this->setAttribute('data-dateISO', 'true');
    }

    /**
     * 获取编辑模式代码
     *
     * @return string
     */
    public function getEditCode() {
        TagFactory::getInstance()->loadCss("datetimepicker", assets("css/plugins/datepicker/datepicker3.css"));
        // TagFactory::getInstance()->loadJs("moment", assets("js/moment.js"));
        TagFactory::getInstance()->loadJs("datetimepicker", assets("js/plugins/datepicker/bootstrap-datepicker.js"));
        
        $option = "{format:  'yyyy-mm-dd', todayBtn: 'linked',autoclose: true,todayHighlight: true,forceParse: false}";

        $script = "\$('.date-picker').datepicker({$option});";
        TagFactory::getInstance()->loadJscript('date', $script);
        
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'date-picker');
        return parent::getEditCode();
    }
}