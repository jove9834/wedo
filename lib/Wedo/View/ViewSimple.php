<?php
/**
 * 默认视图引擎
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\View;

use Wedo\ViewInterface;
use Wedo\Filesystem;
/**
 * 默认视图引擎
 */
class ViewSimple implements ViewInterface {
    /**
     * 视图模板变量
     *
     * @var array
     */
    protected $_tpl_vars = array();

    /**
     * 模板路径
     *
     * @var array
     */
    protected $_paths = array();

    /**
     * 模板扩展名
     *
     * @var string
     */
    protected $_tpl_ext = '.php';

    /**
     * 为视图引擎分配一个模板变量
     *
     * @param mixed $name  变量名
     * @param mixed $value 变量值
     * @return void
     */
    public function assign($name, $value = NULL) {
        if (! $name) {
            return;
        }

        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->assign($key, $val);
            }
        }
        else {
            $name = trim($name);
            $this->_tpl_vars[$name] = $value;
        }
    }

    /**
     * 宣染视图
     *
     * @param string $tpl      模板, 不含扩展名
     * @param array  $tpl_vars 模板变量
     * @return string
     */
    public function render($tpl, array $tpl_vars = NULL) {
        $this->assign($tpl_vars);
        $file = $this->compile($tpl);
        $engine = new PhpEngine();
        return $engine->evaluatePath($file, $this->_tpl_vars);
    }

    /**
     * 视图宣染
     *
     * @param string $tpl      模板
     * @param array  $tpl_vars 视图数据
     * @return void
     */
    public function display($tpl, array $tpl_vars = NULL) {
        $this->render($tpl, $tpl_vars);
    }

    /**
     * 视图文件是否存在
     *
     * @param string $tpl 模板
     * @return boolean
     */
    public function exists($tpl) {
        $_file = $this->find($tpl);
        return $_file !== FALSE;
    }

    /**
     * 编译模板
     *
     * @param string $tpl 模板
     * @return string 返回编译后的文件全路径
     * @throws \Exception
     */
    public function compile($tpl) {
        $_file = $this->find($tpl);

        if ($_file === FALSE) {
            throw new \Exception('视图文件不存在:{}' . $tpl);
        }

        return $_file;
    }

    /**
     * 编译字符串
     *
     * @param string $content 内容
     * @return string
     */
    public function compileString($content, array $vars = NULL) {
        // 生成临时文件
        $files = new Filesystem();
        $cachePath = DATA_PATH . DIRECTORY_SEPARATOR . 'views';
        $fileName = md5($content) . $this->_tpl_ext;
        $file = $cachePath . DIRECTORY_SEPARATOR . $fileName;
        $files->put($file);

        $this->assign($vars);
        $engine = new PhpEngine();
        return $engine->evaluatePath($file, $this->_tpl_vars);
    }

    /**
     * 添加模板搜索路径
     *
     * @param string|array $location 路径
     * @return void
     */
    public function addLocation($location) {
        if (! is_array($location)) {
            $location = [$location];
        }
        
        $this->_paths = array_merge($location, $this->_paths);
    }

    /**
     * 获取模板路径
     *
     * @return array
     */
    public function getPaths() {
        return $this->_paths;
    }

    /**
     * 获取模板文件扩展名
     *
     * @return string
     */
    public function getTemplateExt() {
        return $this->_tpl_ext;
    }

    /**
     * 查找模板文件
     *
     * @param string $tpl 模板文件，不含扩展名
     * @return string|FALSE
     */
    protected function find($tpl) {
        foreach ($this->_paths as $path) {
            $file = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $tpl . $this->_tpl_ext;
            if (file_exists($file)) {
                return $file;
            }
        }

        return FALSE;
    } 
}