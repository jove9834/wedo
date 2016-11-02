<?php
/**
 * JsTree组件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui\Tree;

use Wedo\Ui\TagFactory;
use Wedo\Ui\Container;
use Wedo\Dispatcher;
/**
 * JsTree组件
 */
class JsTree extends Container {
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

        $url .= '?cmd=' . $cmd;

        $code = wd_print('<div id="{}" {}></div>', $this->id, $attributeHtml) . PHP_EOL;

        $vars = array('tree' => $this, 'url' => $url);
        $jsscript = Dispatcher::getInstance()->getView()->render('jstree', $vars);
        TagFactory::getInstance()->loadJscript('tree-' . $this->id, $jsscript);
        return $code;
    }
}