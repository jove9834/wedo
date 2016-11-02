<?php
/**
 * AjaxTree组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Tree;

use Wedo\Ui\ComponentInterface;
use Wedo\Ui\Container;
use Wedo\Ui\Layout\DefaultLayout;
use Wedo\Filesystem;
use Wedo\Dispatcher;
/**
 * AjaxTree组件
 */
class AjaxTree extends Container {
    /**
     * 组件ID
     *
     * @var string
     */
    protected $id;    
    
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
    protected $cmd = 'load-tree';

    /**
     * 定义表格数据获取的默认参数，即初始的查询条件，只当defer="true"时，该属性才生效。
     *
     * @var string
     */
    protected $params;

    

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
            $cmd = 'load-tree';
        }

        $code = wd_print('<div id="{}" data-dismiss="wedo-ajaxtree" data-url="{}" data-cmd="{}" {}>', $this->id, $url, $cmd, $attributeHtml) . PHP_EOL;
        $code .= $this->getTreeFolder();
        $code .= $this->getTreeItem();
        $code .= '</div>';
        
        return $code;
    }

    /**
     * 树目录结点脚本
     *
     * @return string
     */
    public function getTreeFolder() {
        $children = $this->getChildren('treefolder');
        if ($children) {
            $code = '<script type="text/html" id="tree_folder_' . $this->id . '">' . PHP_EOL;
            $code .= Dispatcher::getInstance()->getView()->compileString($children[0]->getContent());
            $code .= '</script>' . PHP_EOL;
        }
        else {
            $file = Dispatcher::getInstance()->getView()->compile('tree.folder');
            $fs = new Filesystem();             
            $folder_html = $fs->get($file);
            $code = '<script type="text/html" id="tree_folder_' . $this->id . '">' . PHP_EOL;
            $code .= $folder_html;
            $code .= '</script>' . PHP_EOL;
        }

        return $code;
    }

    /**
     * 树结点脚本
     *
     * @return string
     */
    public function getTreeItem() {
        $children = $this->getChildren('treeitem');
        if ($children) {
            $code = '<script type="text/html" id="tree_item_' . $this->id . '">' . PHP_EOL;
            $code .= Dispatcher::getInstance()->getView()->compileString($children[0]->getContent());
            $code .= '</script>' . PHP_EOL;
        }
        else {
            $file = Dispatcher::getInstance()->getView()->compile('tree.item');
            $fs = new Filesystem();             
            $item_html = $fs->get($file);
            $code = '<script type="text/html" id="tree_item_' . $this->id . '">' . PHP_EOL;
            $code .= $item_html;
            $code .= '</script>' . PHP_EOL;
        }

        return $code;
    }
}