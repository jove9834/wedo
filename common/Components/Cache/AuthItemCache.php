<?php
/**
 * 授权项缓存类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Components\Cache;

use Common\Core\CacheAbstract;
use Common\Models\NodeModel;
use Wedo\Cache\Cache;

/**
 * 授权项缓存类
 */
class AuthItemCache extends CacheAbstract {
    /**
     * 获取缓存KEY
     *
     * @param mixed $key 缓存KEY
     * @return string
     */
    public function getCacheKey($key = NULL) {
        return 'auth_item';
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
        $nodes = NodeModel::instance()->getAll();
        foreach ($nodes as $node) {
            $this->appendAuthItems($data, $node);
        }

        $this->setData($data, $cache_key);
        return $data;
    }

    /**
     * 添加授权项下的路由
     *
     * @param array $nodeEntry 授权项
     * @return void
     */
    private function appendAuthItems(&$data, $nodeEntry) {
        if (! $nodeEntry) {
            return;
        }

        $routes = $nodeEntry['routes'];
        $module = $nodeEntry['module'];
        $key = $nodeEntry['key'];
        $node = $nodeEntry['node'];
        
        $authKey = wd_print('{}/{}/{}', $module, $key, $node);
        $routes = explode(',', $routes);
        foreach ($routes as $val) {
            $data[$val] = $authKey;
        }
    } 
}