<?php
/**
 * 视图引擎接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

/**
 * 视图引擎接口
 */
interface ViewInterface {
    /**
     * 分配模板变量
     *
     * @param mixed $name  变量名称
     * @param mixed $value 值
     * @return boolean
     */
    public function assign($name, $value = NULL);
    /**
     * 显示
     *
     * @param string $tpl      模板
     * @param array  $tpl_vars 视图数据
     * @return boolean
     */
    public function display($tpl, array $tpl_vars = NULL);
    /**
     * 视图宣染
     *
     * @param string $tpl      模板
     * @param array  $tpl_vars 视图数据
     * @return void
     */
    public function render($tpl, array $tpl_vars = NULL);
    /**
     * 视图文件是否存在
     *
     * @param string $tpl 模板
     * @return boolean
     */
    public function exists($tpl);
    /**
     * 编译模板
     *
     * @param string $tpl 模板
     * @return string 返回编译后的文件全路径
     */
    public function compile($tpl);
    /**
     * 编译字符串
     *
     * @param string $content 内容
     * @return string
     */
    public function compileString($content);
    /**
     * 添加模板搜索路径
     *
     * @param string $path 模板搜索路径
     * @return void
     */
    public function addLocation($path);
    public function getPaths();
    public function getTemplateExt();
}