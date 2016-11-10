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

use Apps\Sys\Entity\LogType;
use Common\BaseModel;

/**
 * <<文件说明>>
 */
class LogTypeModel extends BaseModel {
    /**
     * 实体类名称
     *
     * @var string
     */
    protected $entityClass = 'Apps\Sys\Entity\LogType';

    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_log_type';

    /**
     * 表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 值唯一的字段
     *
     * @var string|array
     */
    protected $uniqueColumn = NULL;

    /**
     * 根据ID获取日志类型实体对象
     *
     * @param integer $id 日志类型ID
     * @return LogType
     */
    public function getLogType($id) {
        if (! $id) {
            return NULL;
        }

        $id = intval($id);
        return $this->get($id)->entity();
    }

    /**
     * 根据log key获取日志类型实体对象
     *
     * @param string $logKey 日志类型
     * @return LogType
     */
    public function getLogTypeByLogKey($logKey) {
        if (! $logKey) {
            return NULL;
        }

        return $this->get(array('log_key' => $logKey))->entity();
    }

    /**
     * 获取指定菜单的所有日志类型
     *
     * @param integer $menuItemId 菜单项ID
     * @return array|null 返回array<LogType>
     */
    public function getMenuLogTypes($menuItemId) {
        if (! $menuItemId) {
            return NULL;
        }

        $menuItemId = intval($menuItemId);
        return $this->getAll(array('menu_item_id' => $menuItemId), '*', 'name')->entityResult();
    }

    /**
     * 删除菜单项下的所有日志类型
     *
     * @param integer $menuItemId 菜单项ID
     * @return int 返回被删除的记录数
     */
    public function deleteByMenuItemId($menuItemId) {
        if (! $menuItemId) {
            return 0;
        }

        $menuItemId = intval($menuItemId);
        return $this->deleteByWhere(array('menu_item_id' => $menuItemId));
    }

}