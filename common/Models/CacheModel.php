<?php
/**
 * 缓存模型类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Models;

use Common\BaseModel;

/**
 * 缓存管理
 */
class CacheModel extends BaseModel {
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_cache';

    /**
     * 表主键
     *
     * @var string
     */
    protected $primaryKey = 'cache_key';

    /**
     * 值唯一的字段
     *
     * @var string|array
     */
    protected $uniqueColumn = NULL;
    
    /**
     * 获取缓存配置信息
     *
     * @param string $name 名称
     * @return array
     */
    public function getCache($name) {
        return $this->get(array('cache_key' => $name))->row();
    }

    /**
     * 获取模块下的所有缓存项
     *
     * @param string $module 模块
     * @return array
     */
    public function getDataByModule($module) {
        $rows = $this->getAll(array('module' => $module))->result();
        return $rows;
    }

    /**
     * 删除模块下的所有缓存项
     *
     * @return boolean
     */
    public function deleteByModule($module) {
        return $this->deleteByWhere(array('module' => $module));
    }

    /**
     * 更新模块缓存配置
     *
     * @param array  $caches 缓存配置
     * @param string $module 模块名称
     * @return void
     */
    public function updateCaches($caches, $module) {
        if (! $caches) {
            return;
        }

        // 删除模块下的所有监听
        $this->deleteByModule($module);
        foreach ($caches as $key => $item) {
            $data = array('module' => $module);
            $data['cache_key'] = $key;
            $data['class_name'] = $item['class'];
            $data['key_rule'] = $item['name_rule'];
            $data['description'] = $item['description'];
            $data['update_time'] = time();
            $this->add($data);
        }
    }
}