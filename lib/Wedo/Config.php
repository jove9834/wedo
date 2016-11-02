<?php
/**
 * Application
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

use Wedo\Logger;
/**
 * 配置类
 *
 */
class Config {

    /**
     * 加载的配置数据
     *
     * @var array
     */
    protected static $config = array();

    /**
     * 配置文件扩展名
     *
     * @var string
     **/
    protected static $file_ext = '.php';

    /**
     * 已经加载的配置文件
     *
     * @var array
     */
    protected static $is_loaded = array();


    public static function load($file, $module = NULL, $return = FALSE) {
        $file = str_replace(self::$file_ext, '', $file);
        $file_name = $file . self::$file_ext;
        $config_path = ($module ? Dispatcher::getInstance()->getModulePath($module) . $module : BASE_PATH) .'/config/';

        $file_path = $config_path . $file_name;
        $md5_key = md5($file_path);

        if (in_array($md5_key, self::$is_loaded, TRUE)) {
            if ($return === TRUE) {
                return self::$config[$file];    
            }

            return TRUE;
        }

        if (! file_exists($file_path)) {
            Logger::debug('Config file not found ' . $file_path);
            throw new Exception('Config file not found ' . $file_path);
        }
        
        $config = require $file_path;

        if (! isset($config) || ! is_array($config)) {
            Logger::debug('Config file format error ' . $file_path);
            throw new Exception('Config file format error ' . $file_path);    
        }

        self::$config[$file] = $config;        
        self::$is_loaded[] = $md5_key;

        if ($return === TRUE) {
            return $config;
        }

        return TRUE;
    }

    /**
     * 获取配置值
     *
     * @param string $key 配置项
     * @param string $section 配置文件名
     * @return 
     */
    public static function get($key, $section = 'config') {
        $config = wd_array_val(self::$config, $section);
        return wd_array_val($config, $key);
    }

    /**
     * 获取配置值
     *
     * @param string $section 配置文件名
     * @return 
     */
    public static function getAll($section = 'config') {
        return wd_array_val(self::$config, $section);
    }

    /**
     * 设置配置项值
     *
     * @param string $section 配置文件名
     * @return 
     */
    public static function set($key, $value, $section = 'config') {
        self::$config[$section][$key] = $value;
    }
}