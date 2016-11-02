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
class LoginAttempts extends Entity {
    protected $id;
    protected $login;
    protected $ipAddress;
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
    public function getLogin() {
        return $this->login;
    }

    /**
     * @param  mixed $login
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setLogin($login, $adj = NULL) {
        $this->login = $login;
        $this->addCondition('login', $adj);
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