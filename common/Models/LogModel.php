<?php
/**
 * 操作日志模型文件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Models;

use Common\BaseModel;
use Wedo\Dispatcher;

/**
 * 操作日志
 */
class LogModel extends BaseModel {
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_log';
    /**
     * 值唯一的字段
     *
     * @var string|array
     */
    protected $uniqueColumn = NULL;

}