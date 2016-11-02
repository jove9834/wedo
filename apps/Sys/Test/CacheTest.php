<?php
/**
 * 模块测试类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Apps\Sys\Test;

use Wedo\TestCase;
use Wedo\Logger;
use Common\Core\CacheManager;
/**
 * 模块测试类
 */
class CacheTest extends TestCase {
    /**
     * 标题
     *
     * @var string
     */
    protected $suiteTitle = "缓存测试用例";
    
    /**
     * 列表页
     */
    public function _testingModulePath() {
        $modules = CacheManager::getCache('module')->getData();
        Logger::debug($modules);
        $this->assert(! empty($modules));
    }

    public function testingCache() {
        \Wedo\Cache\Cache::set('test_100', 'Hello!');
        \Wedo\Cache\Cache::set('test_101', 'Hello!');
        \Wedo\Cache\Cache::set('test_102', 'Hello!');
        \Wedo\Cache\Cache::set('test_103', 'Hello!');
        \Wedo\Cache\Cache::set('test_104', 'Hello!');
        \Wedo\Cache\Cache::set('test_105', 'Hello!');
        \Wedo\Cache\Cache::delete('test_*');
    }
}