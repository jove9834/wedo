<?php
/**
 * Grid组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Grid;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\Container;
use Wedo\Ui\TagFactory;
use Wedo\Ui\Expression;
use Wedo\Ui\Layout\DefaultLayout;
use Wedo\Filesystem;
/**
 * Grid组件
 */
class Grid extends Container {
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
     * 每页显示记录数
     *
     * @var integer
     */
    protected $pagesize = 15;

    /**
     * 廷迟加载，以Ajax方式加载
     *
     * @var string
     */
    protected $defer;

    /**
     * 定义表格数据获取的URL地址
     *
     * 当defer="true"时，该属性才生效，如果为空，则默认取当前控制器的ajax方法；
     * 注意，url所指向的控制器必须实现ajaxSearch方法
     *
     * @var string
     */
    protected $url;

    /**
     * Ajax请求命令
     *
     * 当defer="true"时，该属性才生效，如果为空，则默认取当前控制器的ajax方法；
     * 注意，url所指向的控制器必须实现ajaxSearch方法
     *
     * @var string
     */
    protected $cmd = 'search';

    /**
     * 定义表格数据获取的默认参数，即初始的查询条件，只当defer="true"时，该属性才生效。
     *
     * @var string
     */
    protected $params;

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
     * 设置ID
     *
     * @param mixed $value 值
     * @return void
     */
    public function setId($value) {
        $this->id = $value;
    }

    /**
     * 获取ID
     *
     * @return void
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 设置Pagination
     *
     * @param mixed $value 值
     * @return void
     */
    public function setPagination($value) {
        $this->pagination = $value;
    }

    /**
     * 获取Pagination
     *
     * @return void
     */
    public function getPagination() {
        return $this->pagination;
    }

    /**
     * 设置Pagesize
     *
     * @param mixed $value 值
     * @return void
     */
    public function setPagesize($value) {
        $this->pagesize = $value;
    }

    /**
     * 获取Pagesize
     *
     * @return void
     */
    public function getPagesize() {
        return $this->pagesize;
    }

    /**
     * 设置Defer
     *
     * @param mixed $value 值
     * @return void
     */
    public function setDefer($value) {
        $this->defer = $value;
    }

    /**
     * 获取Defer
     *
     * @return void
     */
    public function getDefer() {
        return $this->defer;
    }

    /**
     * 设置Url
     *
     * @param mixed $value 值
     * @return void
     */
    public function setUrl($value) {
        $this->url = $value;
    }

    /**
     * 获取Url
     *
     * @return void
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * 设置Cmd
     *
     * @param mixed $value 值
     * @return void
     */
    public function setCmd($value) {
        $this->cmd = $value;
    }

    /**
     * 获取Cmd
     *
     * @return void
     */
    public function getCmd() {
        return $this->cmd;
    }

    /**
     * 设置Params
     *
     * @param mixed $value 值
     * @return void
     */
    public function setParams($value) {
        $this->params = $value;
    }

    /**
     * 获取Params
     *
     * @return void
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * 设置是否多选
     *
     * @param mixed $value 值
     * @return void
     */
    public function setMultiSelect($value) {
        if ($value instanceof Expression) {
            $this->multiSelect = $value->getBooleanValue();
        }
        else {
            $this->multiSelect = (bool)$value;
        }
    }


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

        if ($this->defer) {
            $code .= '<tbody></tbody>' . PHP_EOL;        
            $code .= '</table><div class="col-sm-12"><div class="btn-group pull-right pagination"></div></div>' . PHP_EOL;
            if ($this->url) {
                $url = $this->url;
            }
            else {
                $url = wd_controller_url('ajax', $this->params);
            }

            if ($this->cmd) {
                $cmd = $this->cmd;
            }
            else {
                $cmd = 'search';
            }
            
            $code = wd_print('<div class="wd-table" id="{}" data-dismiss="table" data-defer="true" data-url="{}" data-cmd="{}" data-pagesize="{}">{}</div>', 
                $this->id,
                $url,
                $cmd,
                $this->getPagesize(),
                $code);
            $this->cacheBodyTemplate();

        }
        else {
            $code .= '<tbody>' . PHP_EOL;        
            $code .= $this->getTableBody();
            $code .= '</tbody></table>' . PHP_EOL;
        }

        return $code;
    }

    /**
     * 缓存表格数据模板
     *
     * @return void
     */
    public function cacheBodyTemplate() {
        $code = $this->getTableBody();
        // 对code 进行模板编译
        $content = TagFactory::getInstance()->compileString($code);

        $fs = new Filesystem();
        $fs->put(self::getCacheFile($this->id), $content);
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
            $templateContent[] = '<td><input type="checkbox" name="ids[]" class="i-checks" value="<?php echo wd_array_val($row, \'' . $this->primaryKey . '\');?>"></td>';
        }
        
        foreach ($children as $cell) {
            $templateContent[] = $cell->getBody();
        }

        $ds = '$ds_' . $this->id;
        $tr_code = implode(PHP_EOL, $templateContent);
        $code = '<?php foreach (' . $ds . ' as $row): ?>' . PHP_EOL;
        $code .= '<tr>' . $tr_code . '</tr>' . PHP_EOL;
        $code .= '<?php endforeach;?>' . PHP_EOL;

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