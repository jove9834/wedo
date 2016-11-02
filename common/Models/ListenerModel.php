<?php
/**
 * 监听模型类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Models;

use Common\BaseModel;

/**
 * 监听管理
 */
class ListenerModel extends BaseModel {
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_listener';

    /**
     * 表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 值唯一的字段
     *
     * @var string|array
     */
    protected $uniqueColumn = array('event_name', 'class_name');

    /**
     * 获取指定事件名称下的所有监听
     *
     * @param string $eventName 事件名称
     * @return array
     */
    public function getListeners($eventName) {
        $rows = $this->getAll(array('event_name' => $eventName));
        if (! $rows) {
            return NULL;
        }

        $result = array();
        foreach ($rows as $row) {
            $result[] = $row['class_name'];
        }

        // cache

        return $result;
    }
    
    /**
     * 更新模块监听配置
     *
     * @param array  $listeners 监听配置
     * @param string $module    模块名称
     * @return void
     */
    public function updateListeners(array $listeners, $module) {
        if (! $listeners) {
            return;
        }

        // 删除模块下的所有监听
        $this->deleteByModule($module);
        foreach ($listeners as $key => $item) {
            $data = array('module' => $module);
            $data['event_name'] = $key;
            $data['class_name'] = $item['class'];
            $data['type'] = $item['type'];
            $data['description'] = $item['description'];
            $data['update_time'] = time();
            $this->add($data);
        }
    }

    /**
     * 获取模块下的所有监听
     *
     * @param string $module 模块
     * @return array
     */
    public function getDataByModule($module) {
        $rows = $this->getAll(array('module' => $module));        
        return $rows;
    }

    /**
     * 删除模块下的所有监听
     *
     * @return boolean
     */
    public function deleteByModule($module) {
        return $this->deleteByWhere(array('module' => $module));
    }

    
}