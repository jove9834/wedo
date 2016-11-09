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
class LogData extends Entity {
    protected $id;
    protected $requestUuid;
    protected $conn;
    protected $table;
    protected $primaryKey;
    protected $oldData;
    protected $updateData;
    protected $createAt;
    protected $operateType;
    protected $uid;

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
        return $this;
    }
    public function getRequestUuid() {
        return $this->requestUuid;
    }

    /**
     * @param  mixed $requestUuid
     * @param  string $adj 条件修饰符
     * @return  $this
     */
    public function setRequestUuid($requestUuid, $adj = NULL) {
        $this->requestUuid = $requestUuid;
        $this->addCondition('requestUuid', $adj);
        return $this;
    }
    public function getConn() {
        return $this->conn;
    }

    /**
     * @param  mixed $conn
     * @param  string $adj 条件修饰符
     * @return  $this
     */
    public function setConn($conn, $adj = NULL) {
        $this->conn = $conn;
        $this->addCondition('conn', $adj);
        return $this;
    }
    public function getTable() {
        return $this->table;
    }

    /**
     * @param  mixed $table
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setTable($table, $adj = NULL) {
        $this->table = $table;
        $this->addCondition('table', $adj);
        return $this;
    }
    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    /**
     * @param  mixed $primaryKey
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setPrimaryKey($primaryKey, $adj = NULL) {
        $this->primaryKey = $primaryKey;
        $this->addCondition('primaryKey', $adj);
        return $this;
    }
    public function getOldData() {
        return $this->oldData;
    }

    /**
     * @param  mixed $oldData
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setOldData($oldData, $adj = NULL) {
        $this->oldData = $oldData;
        $this->addCondition('oldData', $adj);
        return $this;
    }
    public function getUpdateData() {
        return $this->updateData;
    }

    /**
     * @param  mixed $updateData
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setUpdateData($updateData, $adj = NULL) {
        $this->updateData = $updateData;
        $this->addCondition('updateData', $adj);
        return $this;
    }
    public function getCreateAt() {
        return $this->createAt;
    }

    /**
     * @param  mixed $createAt
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setCreateAt($createAt, $adj = NULL) {
        $this->createAt = $createAt;
        $this->addCondition('createAt', $adj);
        return $this;
    }
    public function getOperateType() {
        return $this->operateType;
    }

    /**
     * @param  mixed $operateType
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setOperateType($operateType, $adj = NULL) {
        $this->operateType = $operateType;
        $this->addCondition('operateType', $adj);
        return $this;
    }
    public function getUid() {
        return $this->uid;
    }

    /**
     * @param  mixed $uid
     * @param  string $adj 条件修饰符
     * @return $this
     */
    public function setUid($uid, $adj = NULL) {
        $this->uid = $uid;
        $this->addCondition('uid', $adj);
        return $this;
    }

}