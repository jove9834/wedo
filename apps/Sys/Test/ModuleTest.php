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
use Common\Components\Module;
use Wedo\Logger;
/**
 * 模块测试类
 */
class ModuleTest extends TestCase {
    /**
     * 标题
     *
     * @var string
     */
    protected $suiteTitle = "模块测试用例";
    
    /**
     * 列表页
     */
    public function testingModulePath() {
        $sys_install_path = Module::getInstallPath('demo');
        // Logger::debug('sys install path: {}', $sys_install_path);
        $this->assertEquals($sys_install_path, BASE_PATH . '/apps/Demo/Install/');
    }

    public function testingInitModuleConfig() {
        $config = Module::initModuleConfig('demo');
        // Logger::debug($config);
        $this->assert(TRUE);
    }

    public function testingInstallModule() {
        $ret = Module::install('sys');
        // $ret = Module::install('demo');
        $this->assert($ret);
    }

    public function _testingUninstall() {
        $ret = Module::uninstall('demo');
        // $ret = Module::uninstall('');
        $this->assert($ret);
    }

    public function _testingUpdateConfig() {
        $ret = Module::updateConfig('sys');
        $this->assert($ret);
    }

    // public function testingEntity() {
    //     $module = new \Apps\Sys\Models\Entity\Module;
    //     $module->name = 'sys';
    //     $module->version = '1.0';
    //     $module->getFields();
    // }
}