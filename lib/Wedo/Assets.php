<?php
/**
 * 资源类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */

namespace Wedo;


/**
 * 资源类
 */
class Assets {
    /**
     * 加载CSS资源文件
     *
     * @param string $file   文件名
     * @param string $module 模块名称     
     * @return string
     */
    public static function css($file, $module = NULL) {
        $result = [];
        $files = self::load($module, $file, 'css');  
        foreach ($files as $f) {
            $result[] = wd_print('<link rel="stylesheet" type="text/css" href="{}">', base_url($f));
        }        

        return implode(PHP_EOL, $result);
    }

    /**
     * 加载JS资源文件
     *
     * @param string $file   文件名
     * @param string $module 模块名称     
     * @return string
     */
    public static function js($file, $module = NULL) {
        $result = [];
        $files = self::load($module, $file, 'js');  
        foreach ($files as $f) {
            $result[] = wd_print('<script src="{}"></script>', base_url($f));
        }        

        return implode(PHP_EOL, $result);
    }

    /**
     * 返回模块的图片资源URI
     *
     * @param string $file   文件名
     * @param string $module 模块名称     
     * @return string
     */
    public static function img($file, $module = NULL) {
        if (! $module) {
            $module = Dispatcher::instance()->getRequest()->getModule();
        }
        
        $uri = 'cache/' . $module .'/img/' . $file;
        return base_url($uri);
    }   

    /**
     * 加载资源文件
     *
     * @param string $module 模块名称
     * @param string $file   文件名
     * @param string $type   类型，css, js, img
     * @return array
     */
    protected static function load($module, $file, $type) {
        $fs = new Filesystem();        

        if (! $module) {
            $module = Dispatcher::instance()->getRequest()->getModule();
        }

        $modulePath = Dispatcher::instance()->getModulePath($module);

        $result = array();
        if (! is_array($file)) {
            $file = explode(',', $file);
        }

        $path = 'cache/' . $module .'/' . $type;
        $cacheFileAbsPath = public_path($path);
        if (!($fs->exists($cacheFileAbsPath) && $fs->isDirectory($cacheFileAbsPath))) {
            $fs->makeDirectory($cacheFileAbsPath, 0755, TRUE);
        }
        
        // Logger::debug('path:{}', $cacheFileAbsPath);
        foreach ($file as $f) {
            $filePath = $modulePath . '/Static/' . ltrim($f, '/');
            $content = $fs->get($filePath);                        
            $cacheFile = '/' . md5($f) . '.' . $fs->extension($f);
            Logger::debug('cacheFile:{}', $cacheFile);
            $fs->put($cacheFileAbsPath . $cacheFile, $content);    

            $result[] = $path. $cacheFile;
        }
        
        return $result;
    }
}