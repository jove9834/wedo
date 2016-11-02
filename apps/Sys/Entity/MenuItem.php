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

use Wedo\Database\Entity;

/**
 * <<实体文件说明>>
 */
class MenuItem extends Entity {
    protected $id;
    protected $menuId;
    protected $type;
    protected $name;
    protected $icon;
    protected $url;
    protected $displayOrder;
    protected $status;

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
    public function getMenuId() {
        return $this->menuId;
    }

    /**
     * @param  mixed $menuId
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setMenuId($menuId, $adj = NULL) {
        $this->menuId = $menuId;
        $this->addCondition('menuId', $adj);
    }
    public function getType() {
        return $this->type;
    }

    /**
     * @param  mixed $type
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setType($type, $adj = NULL) {
        $this->type = $type;
        $this->addCondition('type', $adj);
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
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @param  mixed $icon
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setIcon($icon, $adj = NULL) {
        $this->icon = $icon;
        $this->addCondition('icon', $adj);
    }
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param  mixed $url
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setUrl($url, $adj = NULL) {
        $this->url = $url;
        $this->addCondition('url', $adj);
    }
    public function getDisplayOrder() {
        return $this->displayOrder;
    }

    /**
     * @param  mixed $displayOrder
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setDisplayOrder($displayOrder, $adj = NULL) {
        $this->displayOrder = $displayOrder;
        $this->addCondition('displayOrder', $adj);
    }
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param  mixed $status
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setStatus($status, $adj = NULL) {
        $this->status = $status;
        $this->addCondition('status', $adj);
    }

}