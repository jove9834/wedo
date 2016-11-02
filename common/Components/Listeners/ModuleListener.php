<?php
/**
 * 模块监听接口类
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
 * 模块监听接口类,实现模块信息变更，更新相应的数据
 */
class ModuleListener implements IListener {
    /**
     * 监听处理
     *
     * @param string $event      事件
     * @param array  $parameters 参数
     * @return void
     */
    public function handle($event, array $parameters = NULL) {
        $module = wd_array_val($parameters, 'module');
        if ($event == 'onModuleUpdate') {
            $this->onModuleUpdate($module);
        }
        else if ($event == 'onModuleDelete') {            
            $this->onModuleDelete($module);
        }
    }

    /**
     * 模块删除事件
     *
     * @param string $module 模块名称
     * @return void
     */
    private function onModuleDelete($module) {
        if (! $module) {
            return;
        }

        // 删除授权项
        NodeModel::instance()->deleteByModule($module);
        // 删除模块监听配置
        ListenerModel::instance()->deleteByModule($module);
        // 删除模块下的缓存配置
        CacheModel::instance()->deleteByModule($module);
    }

    /**
     * 更新模块下的授权项缓存
     *
     * @param string $module 模块名称
     * @return void
     */
    private function onModuleUpdate($module) {
        if (! $module) {
            return;
        }

        CacheManager::getCache('module')->update();
        // 更新授权项缓存
        CacheManager::getCache('authItem')->update();
        // 更新监听配置缓存
        CacheManager::getCache('listener')->update();
        // 更新缓存配置缓存
    }
}