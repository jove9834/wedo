<?php
/**
 * 语言类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

use Wedo\Logger;

/**
 * 语言类
 */
class Language {
    /**
     * 语言KEY对应的翻译列表
     *
     * @var array
     */
    protected $language = array();

    /**
     * 已加载语言文件列表
     *
     * @var array
     */
    protected $isLoaded = array();

    /**
     * 语言文件的查找路径列表
     *
     * @var array
     */
    protected $location = array(CORE_PATH);

    /**
     * 加载一个语言文件
     *
     * @param mixed  $langfile 语言文件名,默认扩展名为.php，语言文件名可不带扩展名
     * @param string $idiom    idiom
     * @return Language
     */
    public function load($langfile, $idiom = '') {
        $langfile = str_replace('.php', '', $langfile);
        $langfile .= '.php';

        if (empty($idiom) || ! ctype_alpha($idiom)) {
            $idiom = defined('LANGUAGE') ? LANGUAGE : 'zh_cn';
        }

        if (isset($this->isLoaded[$langfile]) && $this->isLoaded[$langfile] === $idiom) {
            return $this;
        }

        $lang = array();
        $found = FALSE;
        foreach (self::$location as $path) {
            $file = wd_print('{}/language/{}/{}', $path, $idiom, $langfile);
            if (file_exists($file)) {
                $found = TRUE;
                $ret = include $basepath;
                if ($ret) {
                    $lang = array_merge($lang, $ret);
                }
            }
        }        

        if (! $found) {
            throw new \Exception('Unable to load the requested language file: language/' . $idiom . '/' . $langfile);
        }

        if (! $lang) {
            Logger::error('Language file contains no data: language/' . $idiom . '/' . $langfile);
        }

        $this->isLoaded[$langfile] = $idiom;
        $this->language = array_merge($this->language, $lang);

        Logger::debug('Language file loaded: language/{}/{}', $idiom, $langfile);
        return $this;
    }

    /**
     * 取翻译
     *
     * @param string $key 语言KEY
     * @return string 返回译文，如果不存在，则返回key
     */
    public function get($key) {
        return wd_array_val($this->language, $key, $key);
    }

    /**
     * 添加语言文件的查找路径
     *
     * @param string $path 查找路径
     * @return Language
     */
    public function addLocation($path) {
        $this->location[] = $path;
        return $this;
    }

    /**
     * 添加语言文件的查找路径, 将该路径优先查找
     *
     * @param string $path 查找路径
     * @return Language
     */
    public function preAddLocation($path) {
        array_unshift($this->location, $path);
        return $this;
    }
}