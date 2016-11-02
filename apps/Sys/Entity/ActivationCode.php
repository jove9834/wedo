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
class ActivationCode extends Entity {
    /**
     * 递增ID
     *
     * @var integer
     */
    protected $id;
    /**
     * 激活码
     *
     * @var string
     */
    protected $code;
    protected $type;
    protected $table;
    protected $tableId;
    protected $createAt;

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
    public function getCode() {
        return $this->code;
    }

    /**
     * @param  mixed $code
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setCode($code, $adj = NULL) {
        $this->code = $code;
        $this->addCondition('code', $adj);
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
    public function getTable() {
        return $this->table;
    }

    /**
     * @param  mixed $table
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setTable($table, $adj = NULL) {
        $this->table = $table;
        $this->addCondition('table', $adj);
    }
    public function getTableId() {
        return $this->tableId;
    }

    /**
     * @param  mixed $tableId
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setTableId($tableId, $adj = NULL) {
        $this->tableId = $tableId;
        $this->addCondition('tableId', $adj);
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