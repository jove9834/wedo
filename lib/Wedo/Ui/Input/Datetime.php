<?php
/**
 * 日期时间输入组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Input;

use Wedo\Ui\TagFactory;

/**
 * Datetime 组件
 */ 
class Datetime extends Text {
    /**
     * 获取编辑模式代码
     *
     * @return string
     */
    public function getEditCode() {
        TagFactory::getInstance()->loadCss("datetimepicker", assets("css/plugins/datepicker/datepicker3.css"));
        // TagFactory::getInstance()->loadJs("moment", assets("js/moment.js"));
        TagFactory::getInstance()->loadJs("datetimepicker", assets("js/plugins/datepicker/bootstrap-datepicker.js"));

        $option = "{format: 'yyyy-mm-dd hh:ii', todayBtn: 'linked', autoclose: true,todayHighlight: true, forceParse: false}";

        $script = "\$('.datetime-picker').datepicker({$option});";
        TagFactory::getInstance()->loadJscript('datetime', $script);
        
        $this->attributes = self::appendAttributeValue($this->getAttributes(), 'class', 'datetime-picker');
        return parent::getEditCode();
    }
}