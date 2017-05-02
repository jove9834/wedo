<?php
/**
 * 接口类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Core;

use Wedo\Cache\Cache;

abstract class CacheAbstract {
    /**
     * 获取缓存KEY
     *
     * @param mixed $key 缓存KEY
     * @return string
     */
    abstract function getCacheKey($key = NULL);

    /**
     * 取数据
     *
     * @param mixed $key 缓存KEY
     * @return mixed
     */
    public function getData($key = NULL) {
        return Cache::get($this->getCacheKey($key));
    }

    /**
     * 删除
     *
     * @param mixed $key 缓存KEY
     * @return void
     */
    public function delete($key = NULL) {
        Cache::delete($this->getCacheKey($key));
    }

    /**
     * 设置缓存
     *
     * @param mixed $data 数据
     * @param mixed $key  缓存KEY
     * @return mixed
     */
    public function setData($data, $key = NULL) {
        if (is_array($data)) {
            $data = wd_json_encode($data);
        }

        Cache::set($this->getCacheKey($key), $data);
    }

    /**
     * 更新缓存
     *
     * @param mixed $key 缓存KEY
     * @return boolean
     */
    public function update($key = NULL) {
        // 默认为删除Key
        $this->delete($key);
        return TRUE;
    }
}