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
class Menu extends Entity {
    protected $id;
    protected $name;
    protected $icon;
    protected $pid;
    protected $path;
    protected $hasChild;
    protected $displayOrder;

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
    public function getPid() {
        return $this->pid;
    }

    /**
     * @param  mixed $pid
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setPid($pid, $adj = NULL) {
        $this->pid = $pid;
        $this->addCondition('pid', $adj);
    }
    public function getPath() {
        return $this->path;
    }

    /**
     * @param  mixed $path
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setPath($path, $adj = NULL) {
        $this->path = $path;
        $this->addCondition('path', $adj);
    }
    public function getHasChild() {
        return $this->hasChild;
    }

    /**
     * @param  mixed $hasChild
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setHasChild($hasChild, $adj = NULL) {
        $this->hasChild = $hasChild;
        $this->addCondition('hasChild', $adj);
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

}