<?php
/**
 * 数据库操作模型类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Models;

use Common\BaseModel;
use Wedo\Cache\Cache;

/**
 * 数据库链接管理
 */
class DBModel extends BaseModel {
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_db';
    /**
     * 值唯一的字段
     *
     * @var string|array
     */
    protected $uniqueColumn = NULL;

    /**
     * 获取数据库信息
     *
     * @return void
     */
    public function getDBInfo($id) {
        $data = $this->getCacheData($id);
        if ($data) {
            return $data;
        }

        $data = $this->get($id);
        if ($data) {
            $this->setCacheData($id, $data);
        }

        return $data;
    }

    
    /**
     * 插入后触发
     *
     * @param int   $id   插入的记录ID
     * @param array $data 插入数据内容
     * @return bool
     */
    public function afterInsert($id, array $data) {
        $this->deleteCache($id);
        return TRUE;
    }

    /**
     * 更新后触发
     *
     * @param int  $id    更新的记录ID
     * @param array $data 更新数据内容
     * @return bool
     */
    public function afterUpdate($id, array $newData, array $oldData) {
        $this->deleteCache($id);
        return TRUE;
    }

    /**
     * 删除前触发
     *
     * @param $id 记录ID
     * @return bool
     */
    public function afterDelete($id, array $data) {
        $this->deleteCache($id);
        return TRUE;
    }

    /**
     * 清除缓存
     *
     * @param integer $id ID
     * @return void
     */
    public function deleteCache($id) {
        $cache_key = $this->getCacheKey($id);
        Cache::delete($cache_key);
    }

    /**
     * 取Cache的键值
     *
     * @param integer $id ID
     * @return string cache的键值
     */
    private function getCacheKey($id) {
        return 'sys_db_' . $id;
    }

    /**
     * 取Cache字典数据
     *
     * @param int $id 数据库ID
     * @return array 字典项数组
     */
    private function getCacheData($id) {
        $cache_key = $this->getCacheKey($id);
        $data = Cache::get($cache_key);
        if ($data) {
            $data = json_decode($data, TRUE);
            return $data;  
        }
        else {
            return FALSE;
        }
    }

    /**
     * Cache数据
     *
     * @param integer $id   ID
     * @param array   $data 数据
     * @return void
     */
    private function setCacheData($id, $data) {
        $cache_key = $this->getCacheKey($id);
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        Cache::set($cache_key, $data);     
    }
}