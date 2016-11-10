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

use Apps\Sys\Entity\MenuItem;
use Common\BaseModel;

/**
 * <<文件说明>>
 */
class MenuItemModel extends BaseModel {
    /**
     * 实体类名称
     *
     * @var string
     */
    protected $entityClass = 'Apps\Sys\Entity\MenuItem';

    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_menu_item';

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
     * 根据ID获取菜单项
     *
     * @param integer $id 菜单项ID
     * @return MenuItem | NULL;
     */
    public function getMenuItem($id) {
        if (! $id) {
            return NULL;
        }

        $id = intval($id);
        return $this->get($id)->entity();
    }

    /**
     * 取菜单下所有菜单项
     *
     * @param integer $menuId 菜单ID
     * @return array|null array<MenuItem>
     */
    public function getAllMenuItems($menuId) {
        if (! $menuId) {
            return NULL;
        }

        $menuId = intval($menuId);
        return $this->getAll(array('menu_id' => $menuId), '*', 'display_order DESC, id')->entityResult();
    }

    /**
     * 菜单下是否存在菜单项
     *
     * @param integer $menuId 菜单ID
     * @return boolean TRUE 菜单下存在菜单项，FALSE 不存在
     */
    public function existsMenuItem($menuId) {
        if (! $menuId) {
            return FALSE;
        }

        $menuId = intval($menuId);
        $menuItem = $this->get(array('menu_id' => $menuId))->row();
        return ! empty($menuItem);
    }

    /**
     * 删除前触发事件, 删除菜单项前，先删除菜单项相关的日志类型和权限项
     *
     * @param mixed $id       表主键值
     * @param array $old_data 删除前的数据
     * @throws \Exception
     */
    public function beforeDelete($id, array $old_data) {
        // 删除日志类型
        LogTypeModel::instance()->deleteByMenuItemId($id);
        // TODO: 删除权限项
    }

}