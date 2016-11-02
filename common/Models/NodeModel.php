<?php
/**
 * 授权认证项目
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
 * 授权认证项目
 */
class NodeModel extends BaseModel {
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_node';

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
    protected $uniqueColumn = NULL;

    /**
     * 更新授权认证项目
     *
     * @param array  $authItem   配置文件中的授权节点数组
     * @param string $moduleName 对应的模块名字
     * @param string $category   分类名称
     * @return void
     */
    public function updateAuthorization($authItem, $moduleName, $category) {
        if (! $authItem || ! $moduleName) {
            return;
        }

        // 先删除模块下的结点
        $where = array('module' => $moduleName);
        $this->deleteByWhere($where);

        foreach ($authItem as $key => $node) {
            $data = array();
            $data['type'] = $node['type'];
            $data['category'] = $category;
            $data['module'] = $moduleName;
            $data['key'] = $key;
            $data['name'] = $node['name'];
            $data['node'] = '';
            $data['group'] = wd_array_val($node, 'group', '');            

            // 数据节点处理
            if ($node['type'] === 'data') {
                // 先插入父节点
                $this->add($data);
                // 再处理子节点
                foreach ($node['node'] as $nKey => $subNode) {
                    $data['name'] = $subNode['name'];
                    $routes = $this->wrapControllerMap($moduleName, $subNode['controllerMap']);
                    $data['routes'] = $routes;
                    $data['node'] = $nKey;
                    // self::updateAuthItem( explode( ',', $routes ), true );
                    $this->add($data);
                }
            } else {
                // 普通节点处理
                $data['routes'] = $this->wrapControllerMap($moduleName, $node['controllerMap']);
                // self::updateAuthItem( explode( ',', $data['routes'] ), false );
                $this->add($data);
            }
        }

        // 更新缓存
        // Cache::update( 'authItem' );
    }

    /**
     * 封装控制器与动作映射
     *
     * @param string $module 模块名
     * @param array  $map    控制器与动作的映射数组
     * @return string
     */
    private static function wrapControllerMap($module, $map) {
        $routes = array();
        foreach ($map as $controller => $actions) {
            foreach ($actions as $action) {
                $routes[] = wd_print('{}/{}/{}', $module, $controller, $action);
            }
        }

        return implode(',', $routes);
    }

    /**
     * 删除模块的授权项配置
     *
     * @return boolean
     */
    public function deleteByModule($module) {
        return $this->deleteByWhere(array('module' => $module));
    }
    
    /**
     * 插入后触发
     *
     * @param int   $id   插入的记录ID
     * @param array $data 插入数据内容
     * @return bool
     */
    public function afterInsert($id, array $data) {
        // $this->deleteCache($id);
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
        // $this->deleteCache($id);
        return TRUE;
    }

    /**
     * 删除前触发
     *
     * @param $id 记录ID
     * @return bool
     */
    public function afterDelete($id, array $data) {
        // $this->deleteCache($id);
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