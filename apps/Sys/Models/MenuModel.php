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

use Common\TreeModel;

/**
 * <<文件说明>>
 */
class MenuModel extends TreeModel {
    /**
     * 实体类名称
     *
     * @var string
     */
    protected $entityClass = 'Apps\Sys\Entity\Menu';

    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_menu';

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
     * 根据ID获取菜单
     *
     * @param integer $id 菜单ID
     * @return MenuItem | NULL;
     */
    public function getMenu($id) {
        if (! $id) {
            return NULL;
        }

        $id = intval($id);
        return $this->get($id)->entity();
    }

    /**
     * 获取指定上级菜单ID下的所有子菜单
     *
     * @param integer $parentId 上级菜单ID
     * @param string  $orderBy  排序
     * @return array 返回array<Menu>
     */
    public function getChildren($parentId = 0, $orderBy = 'display_order desc, id') {
        return parent::getChildren($parentId, $orderBy);
    }

    /**
     * 删除前触发事件
     *
     * @param mixed $id       表主键值
     * @param array $old_data 删除前的数据
     * @throws \Exception
     */
    public function beforeDelete($id, array $old_data) {
        // 判断是否存在子菜单
        if ($this->hasChildren($id)) {
            throw new \Exception("该菜单下存在子菜单，请先删除子菜单！");
        }

        // 判断是否存在菜单项
        if (MenuItemModel::instance()->existsMenuItem($id)) {
            throw new \Exception("该菜单下存在菜单项，请先删除菜单项！");
        }

        parent::beforeDelete($id, $old_data);
    }
}