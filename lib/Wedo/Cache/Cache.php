<?php
/**
 * Cache 操作类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Cache;
use Wedo\Cache\Drivers\CacheRedis;
use Wedo\Cache\Drivers\CacheFile;
use Wedo\Logger;

/**
 * Cache 操作类
 */
class Cache {
    /**
     * 缓存实例
     *
     * @var CacheInterface;
     */
    private static $_cache;
    
    private static function isSupported() {
        if (self::$_cache != NULL) {
            return TRUE;
        }
    
        $redis = new CacheRedis();
        if ($redis->isSupported()) {
            Logger::debug('redis supported!');
            self::$_cache = $redis;
            return TRUE;
        }
        
        $file = new CacheFile();
        if ($file->isSupported()) {
            Logger::debug('file supported!');
            self::$_cache = $file;
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * 清除系统缓存
     *
     * @param string $id 缓存名称
     * @return boolean
     */
    public static function delete($id) {
        if (self::isSupported() === FALSE) {
            return FALSE;
        }
        
        $id = strtolower($id);
        return self::$_cache->delete($id);
    }

    /**
     * 清除缓存
     *
     * @param string $type 类型
     * @return boolean
     */
    public static function clean($type = NULL)
    {
        if (self::isSupported() === FALSE) {
            return FALSE;
        }
        
        if($type) {
            $type = strtolower(trim($type));
            $len = strlen($type);
            $cache_array = self::$_cache->cacheInfo(); // memcached缓存
            
            foreach ($cache_array as $key => $value) {
                Logger::debug('dcache clean key['.$key.'] and type['.$type.']');
                if( substr($key, 0, $len) == $type ) {
                    self::delete($key);
                }
            }

            return TRUE;
        } else {
            return self::$_cache->clean(); // memcached缓存
        }
    }

    /**
     * 临时数据缓存读取
     *
     * @param string $id 缓存名称
     * @return array|boolean
     */
    public static function get($id) {
        if (! $id){
            return FALSE;
        }
        
        if (self::isSupported() === FALSE) {
            return FALSE;
        }
                
        $data = self::$_cache->get($id); // memcached缓存
    
        if( $data !== FALSE ) {
            $data = unserialize($data);
        }
        
        return $data;
    }
    
    /**
     * 临时数据缓存
     *
     * @param string  $id   缓存名称
     * @param mixed   $data 缓存数据
     * @param integer $ttl  时间（秒）
     * @return bool
     */
    public static function set($id, $data, $ttl = 0) {
    
        if (! $id) return FALSE;

        if (! is_numeric($ttl)) {
            $ttl = 0;
        }       
        
        if (self::isSupported() === FALSE) {
            return FALSE;
        }
        
        $value = serialize($data);
        
        return self::$_cache->set($id, $value, $ttl);
    }
}