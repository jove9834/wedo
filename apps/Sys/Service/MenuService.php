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
use Apps\Sys\Models\MenuItemModel;
use Apps\Sys\Models\MenuModel;

class MenuService
{
    /**
     * 根据菜单ID获取菜单实体
     *
     * @param integer $menuId 菜单ID
     * @return \Apps\Sys\Models\MenuItem|NULL
     */
    public function getMenu($menuId) {
        return MenuModel::instance()->getMenu($menuId);
    }

    /**
     * 获取菜单项
     *
     * @param integer $menuItemId 菜单项ID
     * @return \Apps\Sys\Entity\MenuItem|NULL
     */
    public function getMenuItem($menuItemId) {
        return MenuItemModel::instance()->getMenuItem($menuItemId);
    }

    /**
     * 获取一级菜单
     *
     * @return array 返回 array<Menu>
     */
    public function getTopMenus() {
        return MenuModel::instance()->getChildren();
    }

    /**
     * 获取子菜单
     *
     * @param integer $menuId 菜单ID
     * @return array 返回 array<Menu>
     */
    public function getChildren($menuId) {
        return MenuModel::instance()->getChildren($menuId);
    }
}