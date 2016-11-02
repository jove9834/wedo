<?php
/**
 * 标签组件工厂类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Ui;

use Wedo\Application;
use Wedo\Dispatcher;
use Wedo\Config;
use Wedo\Logger;

class TagFactory {
    
    /**
     * 标签配置
     *
     * @var Yaf\Config\Ini
     */
    protected $config;

    /**
     * 组件顺序
     *
     * @var integer
     */
    protected $index = 0;

    /**
     * 引入JS文件
     *
     * @var array
     */
    protected $includeJs = array();
    /**
     * 引入CSS文件
     *
     * @var array
     */
    protected $includeCss = array();
    /**
     * 引入Script文件
     *
     * @var array
     */
    protected $includeJscript = array();

    /**
     * 模板编译
     *
     * @var string
     */
    protected $compiler;

    /**
     * 标签前缀
     *
     * @var string
     */
    protected $tagPrefix = 'wd';

    private static $instance = NULL;

    public function __construct(){
        // 加载配置文件
        $this->config = Config::load('tag', NULL, TRUE);

        // 初始化视图环境
        $this->initViewEnvironment();
    }

    /**
     * Get the TagSupport singleton
     *
     * @static
     * @return TagSupport
     */
    public static function &getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new TagFactory();
        }

        return self::$instance;
    }

    /**
     * 根据标签名称获取标签处理类实例
     *
     * @return Tag|NULL
     */
    protected function getTagInstance($class, $parentTag = NULL) {
        if ($class) {
            return new $class($parentTag, $this->index);
        }

        return NULL;
    }

    /**
     * 初始化标签视图环境, 只支持PHP引擎
     *
     * @return void
     */
    private function initViewEnvironment() {
        // $resolver = new \core\view\engines\EngineResolver;
        // $cache = DATAPATH . 'views';
        // $paths = array(public_path('templates/' . TEMPLATE . '/tags'), APPSPATH . 'common/views/tags');        

        // // 注册Php模板解析器
        // $resolver->register('php', function() { return new \core\view\engines\PhpEngine; });

        // $viewFinder = new \core\view\FileViewFinder(App::files(), $paths);
        // $this->viewFactory = new \core\view\Factory($resolver, $viewFinder);

    }

    /**
     * 获取标签视图解析工厂类实例
     *
     * @return \core\view\Factory
     */
    public function view() {
        return Dispatcher::getInstance()->getView();
    }

    public function setCompiler($compiler) {
        $this->compiler = $compiler;
        return $this;
    }

    public function compileString($value) {
        if ($this->compiler) {
            return $this->compiler->compileString($value);
        }

        return $value;
    }

    /**
     * 开始解析
     *
     * @param string            $content   内容
     * @param core\lib\tags\Tag $parentTag 上级标签 
     * @return string 解析后内容
     */
    public function make($content, $parentTag = NULL) {
        if (empty($content)) {
            return NULL;
        }

        $callback = function($matches) use ($parentTag) {
            // Log::debug('TagSupport::callback {}', print_r($matches, TRUE));
            $tagName = trim($matches[1]);
            $attribute_str = trim($matches[2]);
            $tagContent = trim($matches[3]);

            $class = wd_array_val($this->config, strtolower($tagName));
            if (! $class) {
                return $matches[0];
            }

            return $this->handleHtmlElement($attribute_str, $tagContent, $tagName, $class, $parentTag);
        };

        $pattern = sprintf('/<%s:(\w++)((?:"[^"]*"|\'[^\']*\'|[^\'">])*)>(.*)<\/%s:\1>/iUs', $this->tagPrefix, $this->tagPrefix);
        $content = preg_replace_callback($pattern, $callback, $content);
        return $content;
    }
    
    /**
     * 自定义标签处理
     *
     * @param string $attribute_str 标签属性串
     * @param string $content       标签内容
     * @param string $tagName       标签类
     * @param string $parentTag     上级标签对象
     * @return string
     */
    protected function handleHtmlElement($attribute_str, $content, $tagName, $class, $parentTag) {
        $this->index = $this->index + 1;

        // 创建标签实例
        $tag = $this->getTagInstance($class, $parentTag);
        // Log::debug('tagName:{}, class:{}, content:{}', $tagName, $class, $content);
        if (! $tag) {
            Logger::debug('tag is null');
            return $content;
        }
        
        $attributes = $this->parseAttributes($attribute_str);

        // 解析标签
        $content = $tag->make($attributes, $content);        
        
        // 输出引用的脚本
        // $script = $tag->outputScript();
        // return $content . PHP_EOL . $script;
        return $content;
    }

    /**
     * 将字符串：id="id" name="test" 转为属性数组
     *
     * @param string $param 属性字符串
     * @return array 转换后的属性数组
     * @author 
     **/
    protected function parseAttributes($param) {
        $attributes = array();

        preg_match_all('/([a-zA-Z_\-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/U', $param, $out, PREG_PATTERN_ORDER);
                
        foreach ($out[1] as $key => $name) {
            $name = strtolower(trim($name));
            $value = trim($out[2][$key] != '' ? $out[2][$key] : $out[3][$key]);
            if ($value == 'true' || $value == 'false') {
                $value = ($value == 'true');
            }

            $attributes[$name] = $value;
        }

        // Log::debug('attributes:{}', print_r($attributes, TRUE));
        return $attributes;
    }

    /**
     * 加载JS文件
     *
     * @param string $key key
     * @param string $url url
     * @return void
     */
    public function loadJs($key, $url) {
        $this->includeJs[$key] = $url;
    }

    /**
     * 加载CSS文件
     *
     * @param string $key key
     * @param string $url url
     * @return void
     */
    public function loadCss($key, $url) {
        $this->includeCss[$key] = $url;
    }

    /**
     * 加载Script
     *
     * @param string $key    key
     * @param string $script js脚本代码
     * @return void
     */
    public function loadJscript($key, $script) {
        $this->includeJscript[$key] = $script;
    }

    /**
     * 组装脚本
     *
     * @return string
     */
    public function getIncludeScriptString() {
        if (!$this->includeJs && !$this->includeCss && !$this->includeJscript) {
            return '';
        }

        $str = PHP_EOL . "@section('tagUIScript')" . PHP_EOL;
        foreach ($this->includeCss as $url) {
            $str .= '<link rel="stylesheet" type="text/css" href="' . $url . '">' . PHP_EOL;
        }

        foreach ($this->includeJs as $url) {
            $str .= '<script src="' . $url . '" type="text/javascript"></script>' . PHP_EOL;
        }

        if ($this->includeJscript) {
            $script = '<script type="text/javascript">' . PHP_EOL;
            $script .= '$(document).ready(function(){' . PHP_EOL;
            
            foreach ($this->includeJscript as $sc) {
                $script .= $sc . PHP_EOL;
            }

            $script .= '});' . PHP_EOL;
            $script .= '</script>' . PHP_EOL;
            $str .= $script;
        }

        $this->clearScript();
        $str .= "@stop" . PHP_EOL;

        return $str;
    }

    /**
     * 清空
     *
     * @return void
     */
    public function clearScript() {
        $this->includeJs = array();
        $this->includeCss = array();
        $this->includeJscript = array();
    }

}