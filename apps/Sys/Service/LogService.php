<?php
/**
 * <<文件说明>>
 *
 * @author        黄文金
 * @copyright  Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link            http://weidu178.com
 * @since          Version 1.0
 */
namespace Apps\Sys\Service;
use Apps\Sys\Models\LogModel;

class LogService
{
    /**
     * 添加操作日志
     *
     * @param string $function_name 页面名称
     * @param string $operate_type  操作类型
     * @param string $uid           操作人ID
     * @param string $memo          操作说明
     * @return mixed
     */
    public static function writeLog($function_name, $operate_type, $memo = NULL, $uid = FALSE) {
        return LogModel::instance()->writeLog($function_name, $operate_type, $memo, $uid);
    }
}