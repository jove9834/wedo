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
use Wedo\Database\Entity;

/**
 * <<实体文件说明>>
 */
class Log extends Entity {
    /**
     * 日志ID，自动递增
     * @var integer
     */
    protected $id;

    /**
     * 请求UUID， 每次HTTP请求，ID唯一
     * @var string
     */
    protected $requestUuid;

    /**
     * 操作用户ID
     * @var integer
     */
    protected $uid;
    /**
     * 日志KEY，对应日志类型表中的KEY
     * @var string
     */
    protected $logKey;

    /**
     * 操作描述
     * @var string
     */
    protected $description;

    /**
     * 创建时间，时间戳
     * @var integer
     */
    protected $createAt;

    /**
     * 操作客户端IP地址
     * @var string
     */
    protected $ipAddress;

    /**
     * 设备， PC、MOBILE, PAD
     * @var string
     */
    protected $device;

    /**
     * 操作系统
     * @var string
     */
    protected $os;

    /**
     * 浏览器类型
     * @var string
     */
    protected $browserType;

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
    public function getRequestUuid() {
        return $this->requestUuid;
    }

    /**
     * @param  mixed $requestUuid
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setRequestUuid($requestUuid, $adj = NULL) {
        $this->requestUuid = $requestUuid;
        $this->addCondition('requestUuid', $adj);
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
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param  mixed $description
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setDescription($description, $adj = NULL) {
        $this->description = $description;
        $this->addCondition('description', $adj);
    }
    public function getCreateAt() {
        return $this->createAt;
    }

    /**
     * @param  mixed $createAt
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setCreateAt($createAt, $adj = NULL) {
        $this->createAt = $createAt;
        $this->addCondition('createAt', $adj);
    }
    public function getIpAddress() {
        return $this->ipAddress;
    }

    /**
     * @param  mixed $ipAddress
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setIpAddress($ipAddress, $adj = NULL) {
        $this->ipAddress = $ipAddress;
        $this->addCondition('ipAddress', $adj);
    }
    public function getDevice() {
        return $this->device;
    }

    /**
     * @param  mixed $device
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setDevice($device, $adj = NULL) {
        $this->device = $device;
        $this->addCondition('device', $adj);
    }
    public function getOs() {
        return $this->os;
    }

    /**
     * @param  mixed $os
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setOs($os, $adj = NULL) {
        $this->os = $os;
        $this->addCondition('os', $adj);
    }
    public function getBrowserType() {
        return $this->browserType;
    }

    /**
     * @param  mixed $browserType
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setBrowserType($browserType, $adj = NULL) {
        $this->browserType = $browserType;
        $this->addCondition('browserType', $adj);
    }

    /**
     * 获取日志类型实体
     *
     * @return LogType|NULL
     */
    public function getLogType() {
        return LogTypeModel::instance()->getLogTypeByLogKey($this->logKey);
    }

    /**
     * 转换日志记录时间，格式:Y-m-d H:i:s
     *
     * @return false|string
     */
    public function formatCreateAt() {
        return date('Y-m-d H:i:s', $this->createAt);
    }

}