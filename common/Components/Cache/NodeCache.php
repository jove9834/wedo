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
class NodeCache extends CacheAbstract {
    /**
     * 获取缓存KEY
     *
     * @param mixed $key 缓存KEY
     * @return string
     */
    public function getCacheKey($key = NULL) {
        return 'route_' . $key;
    }

    /**
     * 取数据
     *
     * @param mixed $parameter 参数
     * @return mixed
     */
    public function getData($parameter = NULL) {
        $cache_key = $this->getCacheKey($parameter);
        $data = Cache::get($cache_key);
        if ($data) {
            return (array)json_decode($data, TRUE);
        }

        $data = array();
        $all = NodeModel::instance()->get(array('disabled' => 0), 'module');
        if ($all) {            
            foreach ($all as $val) {
                $data[$val['module']] = $val;
            }
        }

        Cache::set($cache_key, wd_json_encode($data));
        return $data;
    }

    /**
     * 删除
     *
     * @param mixed $parameter 参数
     * @return mixed
     */
    public function delete($parameter = NULL) {
        $cache_key = $this->getCacheKey();
        Cache::delete($cache_key);
    }
}