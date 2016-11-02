<?php
/**
 * 资源加载类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

/**
 * 静态资源加载
 */
class ResourceLoader {
    /**
     * 标签配置
     *
     * @var Yaf\Config\Ini
     */
    protected $config;
    /**
     * CSS文件
     * 
     * @var array
     */
    protected $css = array();
    
    /**
     * JS文件
     *
     * @var array
     */
    protected $js = array();
    /**
     * 实例
     *
     * @var array
     */
    private static $instance = NULL;
    /**
     * 单例模式
     *
     * @return ResourceLoader
     */
    public static function getInstance() {
        if (! self::$instance) {
            self::$instance = new ResourceLoader();
        }

        return self::$instance;
    }

    /**
     * 构造函数
     */
    private function __construct() {
        // 加载资源配置
        if (Registry::has('RESOURCE_CONFIG')) {
            $config = Registry::get('RESOURCE_CONFIG');
        }
        else {
            $config = new Ini(BASE_PATH . '/conf/resource.ini');
            Registry::set('RESOURCE_CONFIG', $config);
        }

        if ($config->get(TEMPLATE)) {
            $this->config = $config->get(TEMPLATE);
        }
        else {
            $this->config = $config->get('default');
        }

        Logger::debug('Resource config:{}', $this->config);
    }

    /**
     * 添加CSS文件
     * 
     * @param string $key 文件KEY
     * @param string $url 文件URL
     * @return void
     */
    public function addCss($key, $url) {
        if (! $url) {
            $url = $this->config->{THEME}->css->get($key);
            $config = Registry::get('RESOURCE_CONFIG');
            $url = parse_string($url, (array)$config->base);
        }

        if (! $url) {
            throw new \Exception(wd_print('Css resource key {} not found!', $key));
        }

        $this->css[$key] = $url;
    }

    /**
     * 添加JS文件
     * 
     * @param string $key 文件KEY
     * @param string $url 文件URL
     * @return void
     */
    public function addJs($key, $url) {
        if (! $url) {
            $url = $this->config->{THEME}->js->get($key);
            $config = Registry::get('RESOURCE_CONFIG');
            $url = parse_string($url, (array)$config->base);
        }

        if (! $url) {
            throw new \Exception(wd_print('Js resource key {} not found!', $key));
        }

        $this->js[$key] = $url;
    }

    /**
     * 加载CSS文件
     * 
     * @param string $key 文件KEY
     * @param string $url 文件URL
     * @return void
     */
    public static function loadCss($key, $url = NULL){
        self::getInstance()->addCss($key, $url);
    }
    
    /**
     * 加载JS文件
     *
     * @param string $key 文件KEY
     * @param string $url 文件URL
     * @return void
     */
    public static function loadJs($key, $url = NULL){
        self::getInstance()->addJs($key, $url);
    }
    
    /**
     * 获取CSS文件URL
     * 
     * @return array
     */
    public static function getCss(){
        return array_values(self::getInstance()->css);
    }
    
    /**
     * 获取JS文件URL
     *
     * @return array
     */
    public static function getJs(){
        return array_values(self::getInstance()->js);
    }
}

