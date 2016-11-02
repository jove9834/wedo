<?php
/**
 * 模块管理的API接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */

namespace Common\Components;

use Wedo\Exception\CException;
use Wedo\Application;
use Wedo\Logger;
use Common\Models\ModuleModel;
use Common\Models\NodeModel;
use Common\Models\ListenerModel;
use Common\Models\CacheModel;

/**
 * 模块管理函数库类，提供安装模块，检测模块，协助模块一系列操作功能的实现
 */
class Module {
    /**
     * 模块文件夹别名
     */
    const MODULE_ALIAS = 'apps';

    /**
     * 模块安装文件夹别名
     */
    const INSTALL_PATH_ALIAS = 'install';

    /**
     * 模块卸载文件夹别名
     */
    const UNINSTALL_PATH_ALIAS = 'uninstall';

    /**
     * 路径分隔常量
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * 核心模块
     *
     * @var array 
     */
    private static $_coreModule = array(
        'main', 'user', 'department',
        'position', 'message', 'sys',
        'role'
    );

    /**
     * 核心依赖模块，相比于核心模块的不可关闭与不可卸载来说，它能关闭但不能卸载
     * 因为这种类型的模块在代码与视图层面上与系统核心模块有耦合性
     *
     * @var array 
     */
    private static $_sysDependModule = array(
        'weibo'
    );

    /**
     * 检查某个模块是否可用
     *
     * @param string $moduleName 模块名
     * @return boolean 是否可用
     */
    public static function getIsEnabled($moduleName) {
        static $modules = array();
        if (! $modules) {
            $modules = self::getAllEnabledModule();
        }

        return in_array($moduleName, $modules);
    }

    /**
     * 获得核心模块数组
     *
     * @return array
     */
    public static function getCoreModule() {
        return self::$_coreModule;
    }

    /**
     * 获得核心依赖模块数组
     *
     * @return array
     */
    public static function getDependModule() {
        return self::$_sysDependModule;
    }

    /**
     * 取所有启用的模块名称
     *
     * @return array
     */
    public static function getAllEnabledModule() {
        return ModuleModel::instance()->getAllEnabledModule();
    }

    /**
     * 执行模块安装
     *
     * @param string $moduleName 模块名
     * @throws CException 检查安装文件夹时的异常
     * @return boolean 安装成功与否
     */
    public static function install($moduleName) {
        defined('IN_MODULE_ACTION') or define('IN_MODULE_ACTION', TRUE);
        $checkError = self::check($moduleName);
        if ($checkError) {
            throw new CException($checkError);
        }

        $installPath = self::getInstallPath($moduleName);
        // 安装模块模型(如果有)
        $modelSqlFile = $installPath . 'model.sql';
        if (file_exists($modelSqlFile) ) {
            $modelSql = file_get_contents($modelSqlFile);
            self::executeSql($modelSql);
        }

        // 执行额外的sql语句
        $sqlFiles = glob($installPath . '*.sql');
        if (!empty($sqlFiles)) {
            foreach ($sqlFiles as $sqlFile) {
                if (file_exists($sqlFile) && $sqlFile != $installPath . 'model.sql') {
                    $modelSql = file_get_contents($sqlFile);
                    self::executeSql($modelSql);
                }
            }
        }

        // 处理模块配置，写入数据
        $config = self::initModuleConfig($moduleName);
        $configs = wd_json_encode($config);
        $record = array(
            'module' => $moduleName,
            'name' => $config['param']['name'],
            'url' => $config['param']['url'],
            'category' => $config['param']['category'],
            'version' => $config['param']['version'],
            'description' => $config['param']['description'],
            'icon' => $config['param']['icon'],
            'config' => $configs,
            'install_date' => time(),
            'update_date' => time(),
        );

        if (in_array($moduleName, self::getCoreModule())) {
            $record['is_core'] = 1;
        } elseif (in_array($moduleName, self::getDependModule())) {
            $record['is_core'] = 2;
        } else {
            $record['is_core'] = 0;
        }

        $ret = ModuleModel::instance()->add($record);
        // Cache::rm( 'module' );
        if ($ret) {
            if (isset($config['authorization'])) {
                self::updateAuthorization($config['authorization'], $moduleName, $config['param']['category']);
            }

            if (isset($config['listener'])) {
                // 更新监听配置
                ListenerModel::instance()->updateListeners($config['listener'], $moduleName);
            }

            if (isset($config['cache'])) {
                // 更新缓存配置
                CacheModel::instance()->updateCaches($config['cache'], $moduleName);
            }
        }

        $extentionScript = $installPath . 'extention.php';
        // 执行模块扩展脚本(如果有)
        if ( file_exists( $extentionScript ) ) {
            include_once $extentionScript;
        }

        return $ret !== FALSE;
    }

    /**
     * 执行模块卸载
     * @param string $moduleName 模块名
     * @return boolean
     */
    public static function uninstall($moduleName) {
        defined('IN_MODULE_ACTION') or define('IN_MODULE_ACTION', TRUE);
        $record = ModuleModel::instance()->getModule($moduleName);
        if ($record) {
            ModuleModel::instance()->delete($moduleName);
            // Cache::rm('module');
        }

        $uninstallPath = self::getUninstallPath($moduleName);
        $extentionScript = $uninstallPath . 'extention.php';
        $modelSqlFile = $uninstallPath . 'model.sql';
        Logger::debug('model sql file {}', $modelSqlFile);
        // 卸载模块模型(如果有)
        if (file_exists($modelSqlFile)) {
            $modelSql = file_get_contents($modelSqlFile);
            self::executeSql($modelSql);
        }

        // 执行模块扩展脚本(如果有)
        if (is_file($extentionScript)) {
            include_once $extentionScript;
        }

        return TRUE;
    }

    /**
     * 获取模块安装文件夹路径
     *
     * @param string $module 模块名
     * @return string 
     */
    public static function getConfigPath($module) {
        return Application::app()->getDispatcher()->getModulePath($module) . self::DS . 'config' . self::DS;
    }
    
    /**
     * 获取模块安装文件夹路径
     *
     * @param string $module 模块名
     * @return string 
     */
    public static function getInstallPath($module) {
        return self::getConfigPath($module) . self::INSTALL_PATH_ALIAS . self::DS;
    }

    /**
     * 获取模块卸载文件夹路径
     *
     * @param string $module 模块名
     * @return string 
     */
    public static function getUninstallPath($module) {
        return self::getConfigPath($module) . self::UNINSTALL_PATH_ALIAS . self::DS;
    }

    /**
     * 过滤掉已安装模块,返回未安装模块名
     *
     * @param array $installedModule 已安装模块
     * @param array $moduleDirs      所有模块的文件夹数组
     * @return array
     */
    public static function filterInstalledModule(array $installedModule, array $moduleDirs) {
        $dirs = array();
        foreach ($moduleDirs as $index => $moduleName) {
            if (array_key_exists($moduleName, $installedModule)) {
                continue;
            } else {
                $dirs[] = $moduleName;
            }
        }

        return $dirs;
    }

    /**
     * 初始化模块配置文件
     *
     * @param string $moduleName 模块名称
     * @return array
     */
    public static function initModuleConfig($moduleName) {
        defined('IN_MODULE_ACTION') or define('IN_MODULE_ACTION', TRUE);
        $configPath = self::getConfigPath($moduleName);
        if (is_dir($configPath)) {
            $file = $configPath . 'config.php';
            if (is_file($file) && is_readable($file)) {
                $config = require $file;
            }

            if (isset($config) && is_array($config)) {
                // 处理模块ICON
                $icon = Application::app()->getDispatcher()->getModulePath($moduleName) . '/Static/images/icon.png';
                if (is_file($icon)) {
                    $config['param']['icon'] = 1;
                } else {
                    $config['param']['icon'] = 0;
                }

                // 是否有模块所属分类
                if (!isset( $config['param']['category'])) {
                    $config['param']['category'] = '';
                }
                
                // 是否有首页显示
                if (isset($config['param']['indexShow']) && isset($config['param']['indexShow']['link'])) {
                    $config['param']['url'] = $config['param']['indexShow']['link'];
                } else {
                    $config['param']['url'] = '';
                }
            }

            return $config;
        }

        return array();
    }

    /**
     * 初始化多个模块配置文件 - 参数部分,用于列表
     *
     * @param array $moduleDirs
     * @return array
     */
    public static function initModuleParameters(array $moduleDirs) {
        $modules = array();
        foreach ($moduleDirs as $index => $moduleName) {
            $config = self::initModuleConfig($moduleName);
            if ($param = wd_array_val($config, 'param')) {
                $modules[$moduleName] = $param;
            }
        }

        return $modules;
    }

    /**
     * 更新模块配置
     *
     * @param mixed   $module     要更新的模块名，为空更新全部，可单个模块字符串也可数组格式
     * @param boolean $updateAuth 是否更新授权信息
     * @return boolean
     */
    public static function updateConfig($module = NULL, $updateAuth = TRUE) {    
        defined('IN_MODULE_ACTION') or define('IN_MODULE_ACTION', TRUE);
        $updateList = empty($module) ? array() : (is_array($module) ? $module : array($module));
        $modules = array();
        $installedModule = ModuleModel::instance()->getAllEnabledModule();
        if (!$updateList) {
            $modules = $installedModule;
        } else {
            $modules = $updateList;
        }

        foreach ($modules as $name) {
            if (! in_array($name, $installedModule)) {
                continue;
            }

            $config = self::initModuleConfig($name);
            if (isset($config) && is_array($config)) {
                if (! isset($config['param']['category'])) {
                    $config['param']['category'] = '';
                }

                $data = array(
                    'update_date' => time(),
                    'config' => wd_json_encode($config),
                    'icon' => $config['param']['icon'],
                    'name' => $config['param']['name'],
                    'category' => $config['param']['category'],
                    'version' => $config['param']['version'],
                    'description' => $config['param']['description']
                );

                ModuleModel::instance()->update($name, $data);
                if (isset($config['authorization']) && $updateAuth) {
                    self::updateAuthorization($config['authorization'], $name, $config['param']['category']);
                }

                if (isset($config['listener'])) {
                    // 更新监听配置
                    ListenerModel::instance()->updateListeners($config['listener'], $name);
                }

                if (isset($config['cache'])) {
                    // 更新缓存配置
                    CacheModel::instance()->updateCaches($config['cache'], $name);
                }
            }            
        }

        return TRUE;
    }

    /**
     * 更新授权认证项目
     *
     * @param array  $authItem   配置文件中的授权节点数组
     * @param string $moduleName 对应的模块名字
     * @param string $category   分类名称
     * @return void
     */
    public static function updateAuthorization($authItem, $moduleName, $category) {
        return NodeModel::instance()->updateAuthorization($authItem, $moduleName, $category);
    }

    /**
     * 检查安装所需条件
     *
     * @param string $moduleName 模块名
     * @return boolean 检查通过与否
     */
    private static function check($moduleName) {
        $error = '';
        // 检查是否已安装
        $record = ModuleModel::instance()->getModule($moduleName);
        if ($record) {
            $error = 'This module has been installed';
            return $error;
        }

        // 检查模块安装目录
        $installPath = self::getInstallPath($moduleName);
        if (! is_dir($installPath)) {
            $error = 'Install dir does not exists';
            return $error;
        }

        // 模块配置文件
        $configPath = self::getConfigPath($moduleName);
        if (! file_exists($configPath . 'config.php')) {
            $error = 'Module config missing';
            return $error;
        }

        // 配置文件格式，目前只是粗略匹配
        $configFile = $configPath . 'config.php';
        if (is_file($configFile) && is_readable($configFile)) {
            $config = require $configFile;
        }
        
        $configFormatCorrect = isset($config['param']);
        if (!$configFormatCorrect) {
            $error = 'Module config format error';
            return $error;
        }

        return $error;
    }

    /**
     * 执行mysql.sql文件，创建数据表等
     *
     * @param string $sql sql语句
     * @return void
     */
    public static function executeSql($sql) {
        $sqls = wd_split_sql($sql);
        if (is_array($sqls)) {
            foreach ($sqls as $sql) {
                if (trim($sql) != '') {
                    ModuleModel::instance()->execute($sql);
                }
            }
        } else {
            ModuleModel::instance()->execute($sqls);
        }
    }

}
