<?php
/**
 * Blade视图引擎
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\View;

use Wedo\ViewInterface;
use Wedo\Support\NamespacedItemResolver;
use Exception;
/**
 * Blade视图引擎
 */
class ViewBlade implements ViewInterface {
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
    protected $_tpl_ext = array('.phtml');

    /**
     * All of the finished, captured sections.
     *
     * @var array
     */
    protected $sections = array();

    /**
     * The stack of in-progress sections.
     *
     * @var array
     */
    protected $sectionStack = array();

    /**
     * The number of active rendering operations.
     *
     * @var int
     */
    protected $renderCount = 0;

    /**
     * 为视图引擎分配一个模板变量
     *
     * @param mixed $name  变量名称
     * @param mixed $value 值
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
     * @param string     $tpl      模板
     * @param array|NULL $tpl_vars 变量
     * @return string
     */
    public function make($tpl, array $tpl_vars = NULL) {
        $this->incrementRender();
        $cacheFile = $this->compile($tpl);
        $engine = new PhpEngine();
        $content = $engine->evaluatePath($cacheFile, $tpl_vars);
        $this->decrementRender();
        $this->flushSectionsIfDoneRendering();
        return $content;
    }

    /**
     * 宣染视图
     *
     * @param string $tpl      模板
     * @param array  $tpl_vars 模板变量
     * @return string
     */
    public function render($tpl, array $tpl_vars = NULL) {        
        if ($tpl_vars) {
            $this->assign($tpl_vars);
        }

        $this->assign('__env', $this);

        return $this->make($tpl, $this->_tpl_vars);
    }

    /**
     * 显示
     *
     * @param string $tpl      模板
     * @param array  $tpl_vars 模板变量
     * @return void
     */
    public function display($tpl, array $tpl_vars = NULL) {
        echo $this->render($tpl, $tpl_vars);
    }

    /**
     * 编译模板
     *
     * @param string $tpl 模板
     * @return string
     * @throws \Exception
     */
    public function compile($tpl) {
        $_file = $this->find($tpl);

        if ($_file === FALSE) {
            throw new \Exception('视图文件不存在:' . $tpl);
        }

        $compiler = new BladeCompiler();
        // 判断是否缓存有效
        // if ($compiler->isExpired($_file)) {
            $compiler->compile($_file);
        // }

        $cacheFile = $compiler->getCompiledPath($_file);
        return $cacheFile;
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
     * 编译字符串
     *
     * @param string $content 内容
     * @return string
     */
    public function compileString($content) {
        if (! $content) {
            return $content;
        }

        $compiler = new BladeCompiler();
        return $compiler->compileString($content);
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
        return $this;
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
     * 添加模板扩展名
     *
     * @return array
     */
    public function addTemplateExt($ext) {
        if (! is_array($ext)) {
            $ext = [$ext];
        }
        
        $this->_tpl_ext = array_merge($ext, $this->_tpl_ext);
        return $this;
    }

    /**
     * 获取模板文件扩展名
     *
     * @return array
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
        $nis = new NamespacedItemResolver();
        // 判断tpl是否是由命名空间及Group.Item构成
        list($namespace, $group, $item) = $nis->parseKey($tpl);
        if ($namespace) {
            $path = strtr($namespace, '\\', DIRECTORY_SEPARATOR);
            $path = BASE_PATH . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . 'Views';
            $searchPaths = array($path);
        }
        else {
            $searchPaths = $this->_paths;
        }

        $file = $group;
        if ($item) {
            $file .= '.' . $item;
        }

        \Wedo\Logger::debug("search paths {}", $searchPaths);
        $file = strtr($file, '.', DIRECTORY_SEPARATOR);
        foreach ($this->_tpl_ext as $ext) {
            $_file_name = $file . $ext;
            foreach ($searchPaths as $path) {
                $_file = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $_file_name;
                \Wedo\Logger::debug("find file {}", $_file);
                if (file_exists($_file)) {
                    return $_file;
                }
            }
        }        

        return FALSE;
    } 

    /**
     * Start injecting content into a section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    public function startSection($section, $content = '') {
        if ($content === '') {
            if (ob_start()) {
                $this->sectionStack[] = $section;
            }
        } else {
            $this->extendSection($section, $content);
        }
    }

    /**
     * Inject inline content into a section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    public function inject($section, $content) {
        return $this->startSection($section, $content);
    }

    /**
     * Stop injecting content into a section and return its contents.
     *
     * @return string
     */
    public function yieldSection() {
        return $this->yieldContent($this->stopSection());
    }

    /**
     * Stop injecting content into a section.
     *
     * @param  bool  $overwrite
     * @return string
     */
    public function stopSection($overwrite = false) {
        $last = array_pop($this->sectionStack);

        if ($overwrite) {
            $this->sections[$last] = ob_get_clean();
        } else {
            $this->extendSection($last, ob_get_clean());
        }

        return $last;
    }

    /**
     * Stop injecting content into a section and append it.
     *
     * @return string
     */
    public function appendSection() {
        $last = array_pop($this->sectionStack);

        if (isset($this->sections[$last]))
        {
            $this->sections[$last] .= ob_get_clean();
        }
        else
        {
            $this->sections[$last] = ob_get_clean();
        }

        return $last;
    }

    /**
     * Append content to a given section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    protected function extendSection($section, $content)
    {
        if (isset($this->sections[$section]))
        {
            $content = str_replace('@parent', $content, $this->sections[$section]);
        }

        $this->sections[$section] = $content;
    }

    /**
     * 判断切片是否存在
     *
     * @param string $section
     * @return boolean
     */
    public function sectionExists($section) {
        return isset($this->sections[$section]);
    }

    /**
     * Get the string contents of a section.
     *
     * @param  string  $section
     * @param  string  $default
     * @return string
     */
    public function yieldContent($section, $default = '')
    {
        $sectionContent = $default;

        if (isset($this->sections[$section]))
        {
            $sectionContent = $this->sections[$section];
        }

        $sectionContent = str_replace('@@parent', '--parent--holder--', $sectionContent);

        return str_replace(
            '--parent--holder--', '@parent', str_replace('@parent', '', $sectionContent)
        );
    }

    /**
     * Flush all of the section contents.
     *
     * @return void
     */
    public function flushSections()
    {
        $this->sections = array();

        $this->sectionStack = array();
    }

    /**
     * Flush all of the section contents if done rendering.
     *
     * @return void
     */
    public function flushSectionsIfDoneRendering()
    {
        if ($this->doneRendering()) $this->flushSections();
    }

    /**
     * Increment the rendering counter.
     *
     * @return void
     */
    public function incrementRender()
    {
        $this->renderCount++;
    }

    /**
     * Decrement the rendering counter.
     *
     * @return void
     */
    public function decrementRender()
    {
        $this->renderCount--;
    }

    /**
     * Check if there are no active render operations.
     *
     * @return bool
     */
    public function doneRendering()
    {
        return $this->renderCount == 0;
    }
}