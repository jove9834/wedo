<?php
/**
 * 监听管理类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Core;

use Exception;
use Wedo\Logger;

/**
 * 监听管理类
 */
class ListenerManager {
    /**
     * 触发事件
     *
     * @param string $event      事件
     * @param array  $parameters 参数
     * @return void
     */
    public static function trigger($event, array $parameters = NULL) {
        if (! $event) {
            return;
        }

        $data = CacheManager::getCache('listener')->getData();
        if (! $data && ! isset($data[$event])) {
            return;
        }

        $listeners = $data[$event];
        foreach ($listeners as $class) {
            try {
                self::executeListener($event, $class, $parameters);
            } catch (Exception $e) {
                Logger::error($e->getMessage());
            }
        }
    }

    /**
     * 执行监听
     *
     * @param string $event      监听名称
     * @param string $class      监听类名
     * @param array  $parameters 参数
     * @throws Exception
     */
    private static function executeListener($event, $class, array $parameters) {
        if (! $class) {
            throw new Exception("监听类为空！");
        }

        $listener = new $class;
        if (! $listener instanceof IListener) {
            throw new Exception("监听类必须实现IListener接口");
        }

        $listener->handle($event, $parameters);
    }
}