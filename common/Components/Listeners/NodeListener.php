<?php
/**
 * 授权项监听接口类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Components\Listeners;

use Common\Core\IListener;
use Common\Models\NodeModel;
use Common\Models\ListenerModel;
use Common\Models\CacheModel;

/**
 * 授权项监听接口类,实现模块信息变更，更新相应的数据
 */
class NodeListener implements IListener {
    /**
     * 监听处理
     *
     * @param string $event      事件
     * @param array  $parameters 参数
     * @return void
     */
    public function handle($event, array $parameters = NULL) {
        if ($event == 'onNodeUpdate') {
            
        }
        else if ($event == 'onNodeDelete') {
            $id = wd_array_val($parameters, 'id');
            $this->onNodeDelete($id);
        }
    }

    /**
     * 授权项删除事件
     *
     * @param string $id 授权项ID
     * @return void
     */
    private function onNodeDelete($id) {
        if (! $id) {
            return;
        }

        $nodeEntry = NodeModel::instance()->get($id);
        if (! $nodeEntry) {
            return;
        }

        $routes = $nodeEntry['routes'];
        $module = $nodeEntry['module'];
        $key = $nodeEntry['key'];
        $node = $nodeEntry['node'];
        // 先删除模块下的授权项缓存
        CacheManager::getCache('authItem')->delete(wd_print('{}_*', $module));
        $authKey = wd_print('{}/{}/{}', $module, $key, $node);
        $routes = explode(',', $routes);
        foreach ($routes as $val) {
            CacheManager::getCache('authItem')->setData($val, $authKey);
        }
    }

    /**
     * 授权项更新事件
     *
     * @param string $id 授权项ID
     * @return void
     */
    private function onNodeUpdate($id) {
        if (! $id) {
            return;
        }

        $nodeEntry = NodeModel::instance()->get($id);
        if (! $nodeEntry) {
            return;
        }

        $routes = $nodeEntry['routes'];
        $module = $nodeEntry['module'];
        $key = $nodeEntry['key'];
        $node = $nodeEntry['node'];
        // 先删除模块下的授权项缓存
        CacheManager::getCache('authItem')->delete(wd_print('{}_*', $module));
        $authKey = wd_print('{}/{}/{}', $module, $key, $node);
        $routes = explode(',', $routes);
        foreach ($routes as $val) {
            CacheManager::getCache('authItem')->setData($val, $authKey);
        }
    }
}