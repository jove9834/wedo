<?php
/**
 * Table 组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Grid;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\Container;
use Wedo\Ui\Expression;
use Wedo\Ui\Layout\DefaultLayout;
use Wedo\Filesystem;
/**
 * Table 组件
 */
class Table extends Container {
    /**
     * 组件ID
     *
     * @var string
     */
    protected $id;

    /**
     * 是否分页
     *
     * @var boolean
     */
    protected $pagination = TRUE;

    /**
     * 数据源
     *
     * @var string
     */
    protected $ds;

    /**
     * 变量名称
     *
     * @var string
     */
    protected $varname = 'row';

    /**
     * 主键字段
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 是否多选
     *
     * @var boolean
     */
    protected $multiSelect = FALSE;
    
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
        $attributeHtml = self::composeAttributeString($this->getAttributes());
        $code = '<table class="table table-striped table-hover table-bordered" ' . $attributeHtml . '>' . PHP_EOL;
        $code .= '<thead>' . $this->getTableHeader() . '</thead>' . PHP_EOL;        
        $code .= '<tbody>' . PHP_EOL;        
        $code .= $this->getTableBody();
        $code .= '</tbody>' . PHP_EOL;
        if ($this->pagination) {
            $code .= '<tfoot><div class="wd-pagination"></div></foot>' . PHP_EOL;
        }

        $code .= '</table>' . PHP_EOL;

        return $code;
    }
    
    /**
     * 获取表格内容
     *
     * @return string
     */
    public function getTableBody() {
        $children = $this->getChildren('cell');
        $templateContent = array();
        if ($this->multiSelect) {
            $templateContent[] = '<td><input type="checkbox" name="ids[]" class="i-checks" value="@{{' . $this->varname .'.' . $this->primaryKey . '}}"></td>';
        }
        
        foreach ($children as $cell) {
            $templateContent[] = $cell->getBodyByTable($this->varname);
        }

        $tr_code = implode(PHP_EOL, $templateContent);
        $repeat_name = 'ms-repeat';
        if ($this->varname && $this->varname != 'el') {
            $repeat_name = 'ms-repeat-' . $this->varname;
        }

        $code = '<tr ' . $repeat_name . '="' . $this->ds . '" ms-class="even: index % 2 == 0">' . $tr_code . '</tr>' . PHP_EOL;

        return $code;
    }

    /**
     * 获取表格头
     *
     * @return string
     */
    public function getTableHeader() {
        $templateContent = array();
        if ($this->multiSelect) {
            $templateContent[] = '<th width="34"><input type="checkbox" class="i-checks" data-name="ids[]"></th>' . PHP_EOL;
        }

        $children = $this->getChildren('cell');
        
        $totalWidth = 0;
        foreach ($children as $cell) {
            $totalWidth += $cell->getWidth();
        }

        foreach ($children as $cell) {
            $templateContent[] = $cell->getHeader($totalWidth);
        }
                
        return implode(PHP_EOL, $templateContent);
    }

    /**
     * 获取缓存文件名
     *
     * @return string
     */
    public static function getCacheFile($id) {
        // $file = defined('CURRENT_FUNCTION') ? CURRENT_FUNCTION . '-' . $id : $id;
        $file = $id;
        $cache_file = DATA_PATH . '/views/' . $file;
        return $cache_file;
    }
}