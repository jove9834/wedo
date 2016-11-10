<?php
/**
 * <<文件说明>>
 *
 * @author        黄文金
 * @copyright  Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link            http://weidu178.com
 * @since          Version 1.0
 */
namespace Apps\Sys\Entity;

use Apps\Sys\Models\MenuItemModel;
use Apps\Sys\Models\MenuModel;
use Common\TreeEntity;

/**
 * <<实体文件说明>>
 */
class Menu extends TreeEntity
{
    /**
     * 菜单ID，自动递增
     *
     * @var integer
     */
    protected $id;

    /**
     * 菜单名称
     *
     * @var string
     */
    protected $name;

    /**
     * 菜单图标
     *
     * @var string
     */
    protected $icon;

    /**
     * 显示顺序
     *
     * @var integer
     */
    protected $displayOrder;

    /**
     * 获取菜单ID
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed  $id  菜单ID，作为条件时，可以为数组类型
     * @param string $adj 条件修饰符
     * @return  $this
     */
    public function setId($id, $adj = NULL) {
        $this->id = $id;
        $this->addCondition('id', $adj);
        return $this;
    }

    /**
     * get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * set name
     * @param mixed  $name
     * @param string $adj 条件修饰符
     * @return $this
     */
    public function setName($name, $adj = NULL) {
        $this->name = $name;
        $this->addCondition('name', $adj);
        return $this;
    }

    /**
     * get icon
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * set icon
     *
     * @param mixed  $icon
     * @param string $adj  条件修饰符
     * @return $this
     */
    public function setIcon($icon, $adj = NULL) {
        $this->icon = $icon;
        $this->addCondition('icon', $adj);
        return $this;
    }


    /**
     * 获取显示顺序, 值越大，显示在越前面
     *
     * @return int
     */
    public function getDisplayOrder() {
        return $this->displayOrder;
    }

    /**
     * 设置显示顺序, 值越大，显示在越前面
     *
     * @param mixed  $displayOrder 显示顺序
     * @param string $adj          条件修饰符
     * @return $this
     */
    public function setDisplayOrder($displayOrder, $adj = NULL) {
        $this->displayOrder = $displayOrder;
        $this->addCondition('displayOrder', $adj);
        return $this;
    }

    /**
     * 获取上级菜单实体对象
     *
     * @return Menu
     */
    public function getParentMenu() {
        return MenuModel::instance()->getMenu($this->pid);
    }

    /**
     * 获取下级菜单实体对象
     *
     * @return array 返回array<Menu>
     */
    public function getChildren() {
        return MenuModel::instance()->getChildren($this->id);
    }

    /**
     * 获取菜单下的所有菜单项
     *
     * @return array 返回array<MenuItem>
     */
    public function getMenuItems() {
        return MenuItemModel::instance()->getAllMenuItems($this->id);
    }

    /**
     * 菜单下是否存在子菜单
     *
     * @return boolean
     */
    public function existsChildren() {
        return MenuModel::instance()->hasChildren($this->id);
    }

    /**
     * 菜单下是否存在菜单项
     *
     * @return boolean
     */
    public function existsMenuItem() {
        return MenuItemModel::instance()->existsMenuItem($this->id);
    }

    /**
     * 获取菜单的全称
     *
     * @param string $delimiter 分隔符
     * @return string
     */
    public function getFullName($delimiter = ' - ') {
        $parentName = NULL;
        if ($this->pid) {
            $parent = $this->getParentMenu();
            if ($parent) {
                $parentName = $parent->getFullName($delimiter);
            }
        }

        return $parentName ? $parentName . $delimiter . $this->name : $this->name;
    }

}