<?php
// defined('IN_MODULE_ACTION') or die('Access Denied');
return array(
    'param' => array(
        'name' => '系统管理',
        'description' => '提供系统后台的管理与维护',
        'author' => 'Huang wenjin',
        'version' => '1.1'        
    ),
    'authorization' => array(
        'devel' => array(
            'type' => 'node',
            'name' => '开发权限',
            'controllerMap' => array(
                'default' => array('index', 'add', 'edit', 'del', 'show'),
                'share' => array( 'index', 'show' ),
                'attention' => array( 'index', 'edit', 'show' ),
                'comment' => array( 'getcommentlist', 'addcomment', 'delcomment' )
            )
        ),
        'admin' => array(
            'type' => 'node',
            'name' => '系统管理',
            'controllerMap' => array(
                'review' => array( 'index', 'personal', 'add', 'edit', 'del', 'show' )
            )
        )
    ),
    'listener' => array(
        'onModuleDelete' => array(
            'class' => 'Common\\Comoponents\\Listeners\\ModuleListener',
            'type' => 'module', // 取值为 cache, workflow, user
            'description' => '监听模块删除事件'
        ),
    ),
    'cache' => array(
        'module' => array( // 只管理永久缓存的缓存项
            'class' => 'Common\\Components\\Cache\\ModuleCache',
            'name_rule' => 'sys_module',
            'description' => '缓存所有的模块信息'
        ),
        'authItem' => array(
            'class' => 'Common\\Components\\Cache\\AuthItemCache',
            'name_rule' => 'auth_item',
            'description' => '缓存路由URI对应的权限项'
        ),
        'listener' => array(
            'class' => 'Common\\Components\\Cache\\ListenerCache',
            'name_rule' => 'listener',
            'description' => '缓存监听'
        ),
    ),
    'logType' => array(
        'key' => 'description'
    ),

);
