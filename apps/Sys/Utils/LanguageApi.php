<?php
/**
 * 多语言翻译提供的API接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */

namespace Apps\Sys\Utils;
use apps\sys\set\models\LangModel;
use apps\sys\set\models\LangTranslateModel;

class LanguageApi {

    /**
     * 翻译及收集
     *
     * @param string $name 名称
     * @param string $page 使用的页面
     * @param string $lang 语言
     * @return string 返回FALSE,表示该项不存在, 否则返回译文
     **/
    public static function lang($name, $page = NULL, $lang = 'zh-cn') {
        $langModel = new LangModel();
        return $langModel->lang($name, $page, $lang);
    }

    /**
     * 取全局翻译
     *
     * @param string $name 名称
     * @param string $lang 语言
     * @return string
     */
    public static function getGlobalTranslate($name, $lang = 'zh-cn') {
        $langTranslateModel = new LangTranslateModel();
        return $langTranslateModel->getTrans($name, $lang);
    }

    /**
     * 取页面翻译
     *
     * @param string $name 名称
     * @param string $page 页面名称
     * @param string $lang 语言
     * @return string
     */
    public static function getPageTranslate($name, $page, $lang = 'zh-cn') {
        if (! $page) {
            return NULL;
        }
        
        $langTranslateModel = new LangTranslateModel();
        return $langTranslateModel->getTrans($name, $lang, $page);
    }

    /**
     * 翻译
     *
     * @param string $name  翻译KEY
     * @param string $trans 译文
     * @param string $lang  翻译语言
     * @param string $page  引用对象,默认为NULL,即全局翻译
     * @return boolean
     */
    public static function setTranslate($name, $trans, $lang = 'zh-cn', $page = NULL) {
        $langTranslateModel = new LangTranslateModel();
        return $langTranslateModel->setTrans($name, $trans, $lang, $page);        
    }

    /**
     * 设置中文全局翻译
     *
     * @param string $name 名称
     * @param string $title 中文译文
     * @return void
     */
    public static function addLang($name, $title) {
        $langModel = new LangModel();
        $lang = $langModel->get(array('name' => $name));
        if ($lang) {
            $langModel->update($lang['id'], array('title' => $title));
        }
        else {
            // 新增
            $langModel->add(array('name' => $name, 'title' => $title, 'create_at' => time()));
        }

        // 默认中文全局翻译
        static::setTranslate($name, $title);
    }
}