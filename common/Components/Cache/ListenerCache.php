<?php
/**
 * 监听配置缓存类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Components\Cache;

use Common\Core\CacheAbstract;
use Common\Models\ListenerModel;
use Wedo\Cache\Cache;

/**
 * 监听配置缓存类
 */
class ListenerCache extends CacheAbstract {
    /**
     * 获取缓存KEY
     *
     * @param mixed $key 缓存KEY
     * @return string
     */
    public function getCacheKey($key = NULL) {
        return 'listener';
    }

    /**
     * 取数据
     *
     * @param mixed $key 缓存KEY
     * @return mixed
     */
    public function getData($key = NULL) {
        $cache_key = $this->getCacheKey($key);
        $data = Cache::get($cache_key);
        if ($data) {
            return (array)json_decode($data, TRUE);
        }

        $data = array();
        $listeners = ListenerModel::instance()->getAll();
        foreach ($listeners as $item) {
            $this->appendListener($data, $item);
        }

        $this->setData($data, $cache_key);
        return $data;
    }

    /**
     * 添加授权项下的路由
     *
     * @param array &$data 数据集
     * @param array $entry 监听实体
     * @return void
     */
    private function appendListener(&$data, $entry) {
        if (! $entry) {
            return;
        }

        $eventName = $entry['event_name'];
        $class = $entry['class_name'];
        
        if (! isset($data[$eventName])) {
            $data[$eventName] = [];
        }

        if (! in_array($class, $data[$eventName])) {
            $data[$eventName][] = $class;    
        }
    } 
}