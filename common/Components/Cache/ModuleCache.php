<?php
/**
 * 模块信息缓存类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Components\Cache;

use Common\Core\CacheAbstract;
use Common\Models\ModuleModel;
use Wedo\Cache\Cache;

/**
 * 模块信息缓存类
 */
class ModuleCache extends CacheAbstract {
    /**
     * 获取缓存KEY
     *
     * @param mixed $key 缓存KEY
     * @return string
     */
    public function getCacheKey($key = NULL)
        return 'sys_modules';
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
        $all = ModuleModel::instance()->getAll(NULL, '*', 'module');
        if ($all) {            
            foreach ($all as $val) {
                $data[$val['module']] = $val;
            }
        }

        $this->setData($data, $key);
        return $data;
    }
}