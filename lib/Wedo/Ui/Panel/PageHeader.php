<?php
/**
 * PageHeader 组件
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
 * PageHeader 组件
 */ 
class PageHeader extends Container {
    /**
     * 标题
     *
     * @var string
     */
    protected $title;

    /**
     * 是否有搜索框
     *
     * @var boolean
     */
    protected $searchBox = FALSE;

    /**
     * icon图标
     *
     * @var string
     */
    protected $icon;

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
        return '';
    }
}