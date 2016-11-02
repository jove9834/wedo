<?php
/**
 * JQGrid组件
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
use Wedo\Dispatcher;
use Apps\Sys\Api\GridApi;

/**
 * JQGrid组件
 */
class JQGrid extends Container {
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
     * 定义表格数据获取的URL地址
     *
     * 当defer="true"时，该属性才生效，如果为空，则默认取当前控制器的ajax方法；
     * 注意，url所指向的控制器必须实现ajaxSearch方法
     *
     * @var string
     */
    protected $url;

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
    protected $primaryKey;

    /**
     * 是否多选
     *
     * @var boolean
     */
    protected $multiSelect;
    /**
     * dbgrid key
     *
     * @var boolean
     */
    protected $dbgridKey;

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
        if ($this->url) {
            $url = Expression::getTagExpression($this->url);
        }
        else {
            $this->params = $this->params ?: array();
            $this->params['cmd'] = 'search_' . $this->id;
            $url = wd_controller_url('ajax', $this->params);
        }

        if ($this->dbgridKey) {
            $code = '<?php echo Apps\\Sys\\Api\\GridApi::loadGrid("' . $this->id . '", "' . $this->dbgridKey . '", "' . $url . '"); ?>' . PHP_EOL;
        }
        else {
            // 生成脚本
            $vars = array('grid' => $this, 'url' => $url);
            $code = Dispatcher::getInstance()->getView()->render('jqgrid', $vars);
        }
        
        return $code;
    }   
}