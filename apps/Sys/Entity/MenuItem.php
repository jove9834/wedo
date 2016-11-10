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

use Apps\Sys\Models\LogTypeModel;
use Apps\Sys\Models\MenuModel;
use Wedo\Database\Entity;

/**
 * <<实体文件说明>>
 */
class MenuItem extends Entity {
    /**
     * 菜单项类型, 0 系统内菜单
     */
    const MENU_ITEM_TYPE_SYS = 0;
    /**
     * 菜单项类型, 1 外部链接
     */
    const MENU_ITEM_TYPE_URL = 1;

    /**
     * 状态：0 禁用 1正常， 2开发中, 3 测试中
     */
    const MENU_ITEM_STATUS_DISABLED = 0;
    const MENU_ITEM_STATUS_NORMAL = 1;
    const MENU_ITEM_STATUS_DEVELOP = 2;
    const MENU_ITEM_STATUS_TESTING = 3;
    /**
     * 菜单项ID，自动递增
     *
     * @var integer
     */
    protected $id;

    /**
     * 所属菜单ID
     *
     * @var integer
     */
    protected $menuId;

    /**
     * 菜单项类型, 0 系统内菜单，1外部链接
     *
     * @var integer
     */
    protected $type;

    /**
     * 菜单项名称
     *
     * @var string
     */
    protected $name;

    /**
     * 菜单项图标
     *
     * @var string
     */
    protected $icon;

    /**
     * 菜单项URL
     *
     * @var string
     */
    protected $url;

    /**
     * 显示顺序
     *
     * @var integer
     */
    protected $displayOrder;

    /**
     * 状态：0 禁用 1正常， 2开发中, 3 测试中
     * @var integer
     */
    protected $status;

    /**
     * get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * set id
     *
     * @param mixed  $id  菜单项ID
     * @param string $adj 条件修饰符
     * @return $this
     */
    public function setId($id, $adj = NULL) {
        $this->id = $id;
        $this->addCondition('id', $adj);
        return $this;
    }

    /**
     * get menu id
     * @return int
     */
    public function getMenuId() {
        return $this->menuId;
    }

    /**
     * set menu id
     * @param mixed  $menuId 菜单ID
     * @param string $adj    条件修饰符
     * @return $this
     */
    public function setMenuId($menuId, $adj = NULL) {
        $this->menuId = $menuId;
        $this->addCondition('menuId', $adj);
        return $this;
    }

    /**
     * 获取菜单项类型, 0 系统内菜单，1外部链接
     *
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param  mixed $type
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setType($type, $adj = NULL) {
        $this->type = $type;
        $this->addCondition('type', $adj);
        return $this;
    }
    public function getName() {
        return $this->name;
    }

    /**
     * @param  mixed $name
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setName($name, $adj = NULL) {
        $this->name = $name;
        $this->addCondition('name', $adj);
        return $this;
    }
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @param  mixed $icon
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setIcon($icon, $adj = NULL) {
        $this->icon = $icon;
        $this->addCondition('icon', $adj);
        return $this;
    }
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param  mixed $url
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setUrl($url, $adj = NULL) {
        $this->url = $url;
        $this->addCondition('url', $adj);
        return $this;
    }
    public function getDisplayOrder() {
        return $this->displayOrder;
    }

    /**
     * @param  mixed $displayOrder
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setDisplayOrder($displayOrder, $adj = NULL) {
        $this->displayOrder = $displayOrder;
        $this->addCondition('displayOrder', $adj);
        return $this;
    }
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param  mixed $status
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setStatus($status, $adj = NULL) {
        $this->status = $status;
        $this->addCondition('status', $adj);
        return $this;
    }

    /**
     * 获取所属的菜单实体
     *
     * @return Menu
     */
    public function getMenu() {
        return MenuModel::instance()->getMenu($this->menuId);
    }

    /**
     * 获取菜单的全称
     *
     * @param string $delimiter 分隔符
     * @return string
     */
    public function getFullName($delimiter = ' - ') {
        $parentName = NULL;
        if ($this->menuId) {
            $menu = $this->getMenu();
            if ($menu) {
                $parentName = $menu->getFullName($delimiter);
            }
        }

        return $parentName ? $parentName . $delimiter . $this->name : $this->name;
    }

    /**
     * 获取菜单项下的所有日志类型
     *
     * @return array|null 返回array<LogType>
     */
    public function getLogTypes() {
        return LogTypeModel::instance()->getMenuLogTypes($this->id);
    }

}