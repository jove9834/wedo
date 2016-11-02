<?php
/**
 * 缓存管理类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Core;

use Wedo\Logger;
use Common\Models\CacheModel;

/**
 * 缓存管理类
 */
class CacheManager {
    /**
     * 缓存类
     *
     * @var array
     */
    private static $_cache = array();
    
    /**
     * 执行监听
     *
     * @param string $name 缓存名称
     * @return ICache
     */
    public static function getCache($name) {
        if (! $name) {
            throw new \Exception("缓存名称不能为空！");
        }

        if (isset(self::$_cache[$name])) {
            return self::$_cache[$name];
        }

        $cacheInfo = CacheModel::instance()->getCache($name);
        if (! $cacheInfo || ! $cacheInfo['class_name']) {
            throw new \Exception("缓存配置不存在！");
        }

        $reflection = new \ReflectionClass($cacheInfo['class_name']);
        if (! $reflection->implementsInterface('Common\Core\ICache')) {
            throw new \Exception("缓存类必须实现ICache接口");
        }

        $cache = $reflection->newInstance();
        self::$_cache[$name] = $cache;
        return $cache;
    }
}