<?php
/**
 * <<文件说明>>
 *
 * @author        黄文金
 * @copyright  Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link            http://weidu178.com
 * @since          Version 1.0
 */
namespace Apps\Sys\Models;

use Common\BaseModel;

/**
 * <<文件说明>>
 */
class LogDataModel extends BaseModel {
    /**
     * 实体类名称
     *
     *  string
     */
    protected $entityClass = 'Apps\Sys\Entity\LogData';

    /**
     * 表名
     *
     *  string
     */
    protected $table = 'sys_log_data';

    /**
     * 表主键
     *
     *  string
     */
    protected $primaryKey = 'id';

    /**
     * 值唯一的字段
     *
     *  string|array
     */
    protected $uniqueColumn = NULL;

}