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
class UserAccountIndex extends Entity {
    /**
     * 帐号类型为用户名
     */
    const ACCOUNT_TYPE_USERNAME = 0;
    /**
     * 帐号类型为邮箱地址
     */
    const ACCOUNT_TYPE_EMAIL = 1;
    /**
     * 帐号类型为手机号码
     */
    const ACCOUNT_TYPE_MOBILE = 2;
    /**
     * 主键，递增ID
     *
     * @var integer
     */
    protected $id;

    /**
     * 帐号名称
     *
     * @var string
     */
    protected $account;

    /**
     * 帐号类型，Email, 用户名，手机号
     * @var integer
     */
    protected $type;

    /**
     * 用户ID
     *
     * @var integer
     */
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
    }
    public function getAccount() {
        return $this->account;
    }

    /**
     * @param  mixed $account
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setAccount($account, $adj = NULL) {
        $this->account = $account;
        $this->addCondition('account', $adj);
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

}