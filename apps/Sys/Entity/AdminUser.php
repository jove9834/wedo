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
class AdminUser extends Entity {
    protected $id;
    protected $uid;
    protected $groupId;
    protected $name;
    protected $isDelete;
    protected $ctime;

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
    public function getUid() {
        return $this->uid;
    }

    /**
     * @param  mixed $uid
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setUid($uid, $adj = NULL) {
        $this->uid = $uid;
        $this->addCondition('uid', $adj);
    }
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * @param  mixed $groupId
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setGroupId($groupId, $adj = NULL) {
        $this->groupId = $groupId;
        $this->addCondition('groupId', $adj);
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
    public function getIsDelete() {
        return $this->isDelete;
    }

    /**
     * @param  mixed $isDelete
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setIsDelete($isDelete, $adj = NULL) {
        $this->isDelete = $isDelete;
        $this->addCondition('isDelete', $adj);
    }
    public function getCtime() {
        return $this->ctime;
    }

    /**
     * @param  mixed $ctime
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setCtime($ctime, $adj = NULL) {
        $this->ctime = $ctime;
        $this->addCondition('ctime', $adj);
    }

}