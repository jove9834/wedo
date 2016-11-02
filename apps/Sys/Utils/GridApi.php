<?php
/**
 * Grid的API接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Apps\Sys\Utils;

use Wedo\Cache\Cache;
use Wedo\Dispatcher;
use Wedo\Filesystem;
use Wedo\View\PhpEngine;
use Apps\Sys\Models\GridModel;
use Apps\Sys\Models\GridItemModel;

/**
 * Grid的API接口
 */
class GridApi {
    /**
     * 获取Grid配置
     *
     * @param string $name 名称
     * @return array
     */
    public static function getGrid($name) {
        $gridModel = new GridModel();
        $grid = $gridModel->get(array('name' => $name));
        if (! $grid) {
            throw new \Exception('Gird配置不存在');
        }

        return $grid;
    }

    /**
     * 获取GridItem配置
     *
     * @param integer $grid_id 表格ID
     * @return array
     */
    public static function getGridItems($grid_id) {
        $gridItemModel = new GridItemModel();
        $items = $gridItemModel->getAll(array('grid_id' => $grid_id), '*', 'display_order desc, id');
        if (! $items) {
            throw new \Exception('GridItems配置不存在');
        }

        return $items;
    }

    /**
     * 获取Grid配置
     *
     * @param string $name 名称
     * @return array
     */
    public static function getGridConfig($name) {
        $cache_key = "wd_grid_" . $name;
        $grid = Cache::get($cache_key);
        if ($grid) {
            $grid = json_decode($grid, TRUE);
            return $grid;  
        }

        $grid = self::getGrid($name);
        $items = self::getGridItems($grid['id']);
        $grid['items'] = $items;
        $data = json_encode($grid);
        Cache::set($cache_key, $data);
        return $grid;
    }

    /**
     * 缓存Grid模板
     *
     * @param string $name 名称
     * @return array
     */
    private static function cacheTemplate($name) {
        $grid = self::getGridConfig($name);
        $vars = array('grid' => $grid);
        $code = Dispatcher::getInstance()->getView()->render('dbgrid', $vars);
        $cache_file = self::getCacheFilePath($name);
        $fs = new Filesystem();
        $fs->put($cache_file, $code);
    }

    /**
     * 获取缓存文件路径
     *
     * @param string $name 名称
     * @return string
     */
    private static function getCacheFilePath($name) {
        return DATA_PATH . '/cache/grid/' . $name;
    }

    /**
     * 加载表格
     *
     * @param string $name Grid Name
     * @param string $url  数据源地址
     * @return string
     */
    public static function loadGrid($dom_id, $name, $url) {
        $vars = array('url' => $url, 'grid_id' => $dom_id);        
        $cache_file = self::getCacheFilePath($name);
        $fs = new Filesystem();
        if (! $fs->exists($cache_file)) {
            self::cacheTemplate($name);
        }

        $engine = new PhpEngine();
        $content = $engine->evaluatePath($cache_file, $vars);
        return $content;
    }

    /**
     * 删除缓存
     *
     * @param string $name 名称
     * @return string
     */
    public static function deleteCache($name) {
        $cache_key = "wd_grid_" . $name;
        Cache::delete($cache_key);
        $fs = new Filesystem();
        $fs->delete(self::getCacheFilePath($name));
    }

}