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
use Wedo\Database\Entity;

/**
 * <<实体文件说明>>
 */
class LogType extends Entity {
    /**
     * 日志类型ID，自动递增
     *
     * @var integer
     */
    protected $id;
    /**
     * 日志类型KEY, 唯一
     *
     * @var string
     */
    protected $logKey;

    /**
     * 日志类型名称
     *
     * @var string
     */
    protected $name;

    /**
     * 菜单项ID,关联菜单项实体
     *
     * @var integer
     */
    protected $menuItemId;

    public function getId() {
        return $this->id;
    }

    /**
     * @param  mixed $id
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setId($id, $adj = NULL) {
        $this->id = $id;
        $this->addCondition('id', $adj);
    }
    public function getLogKey() {
        return $this->logKey;
    }

    /**
     * @param  mixed $logKey
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setLogKey($logKey, $adj = NULL) {
        $this->logKey = $logKey;
        $this->addCondition('logKey', $adj);
    }
    public function getName() {
        return $this->name;
    }

    /**
     * @param  mixed $name
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setName($name, $adj = NULL) {
        $this->name = $name;
        $this->addCondition('name', $adj);
    }
    public function getMenuItemId() {
        return $this->menuItemId;
    }

    /**
     * @param  mixed $menuItemId
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setMenuItemId($menuItemId, $adj = NULL) {
        $this->menuItemId = $menuItemId;
        $this->addCondition('menuItemId', $adj);
    }

    /**
     * 获取日志类型对应的菜单项实体对象
     *
     * @return MenuItem | NULL
     */
    public function getMenuItem() {
        return MenuItemModel::instance()->getMenuItem($this->menuItemId);
    }

}