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
class UserProfile extends Entity {
    protected $id;
    protected $uid;
    protected $name;
    protected $value;

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
    public function getValue() {
        return $this->value;
    }

    /**
     * @param  mixed $value
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setValue($value, $adj = NULL) {
        $this->value = $value;
        $this->addCondition('value', $adj);
    }

}