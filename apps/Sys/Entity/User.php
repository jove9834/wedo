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

use Apps\Sys\Models\UserModel;
use Wedo\Database\Entity;

/**
 * <<实体文件说明>>
 */
class User extends Entity {
    /**
     * 用户ID， 自动递增
     *
     * @var integer
     */
    protected $uid;

    /**
     * 用户姓名
     *
     * @var string
     */
    protected $name;

    /**
     * 密码
     *
     * @var string
     */
    protected $password;

    /**
     * 加密代码，6位数
     *
     * @var string
     */
    protected $salt;

    /**
     * 性别. 0 男， 1女
     *
     * @var integer
     */
    protected $gender;

    /**
     * 姓名首字母
     *
     * @var string
     */
    protected $firstLetter;
    /**
     * 记录创建时间戳
     *
     * @var integer
     */
    protected $createAt;

    /**
     * 是否删除, 1是，0否
     *
     * @var integer
     */
    protected $isDel;

    /**
     * 是否激活, 0 未激活，1已激活
     *
     * @var integer
     */
    protected $isActive;

    /**
     * 头像
     *
     * @var string
     */
    protected $avatar;

    public function __construct($uid = NULL) {
        $uid && $this->setUid($uid);
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

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getSalt() {
        return $this->salt;
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
    public function getGender() {
        return $this->gender;
    }

    /**
     * @param  mixed $gender
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setGender($gender, $adj = NULL) {
        $this->gender = $gender;
        $this->addCondition('gender', $adj);
    }

    public function getFirstLetter() {
        return $this->firstLetter;
    }

    /**
     * @param  mixed $firstLetter
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setFirstLetter($firstLetter, $adj = NULL) {
        $this->firstLetter = $firstLetter;
        $this->addCondition('firstLetter', $adj);
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
        $this->addCondition('create_at', $adj);
    }

    public function getIsDel() {
        return $this->isDel;
    }

    /**
     * @param boolean $isDel 是否删除
     * @param string $adj 条件修饰符
     * @return void
     */
    public function setIsDel($isDel, $adj = NULL) {
        $this->isDel = $isDel ? 1 : 0;
        $this->addCondition('isDel', $adj);
    }
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * @param boolean $isActive 是否激活
     * @param string $adj 条件修饰符
     * @return  void
     */
    public function setIsActive($isActive, $adj = NULL) {
        $this->isActive = $isActive ? 1 : 0;
        $this->addCondition('isActive', $adj);
    }

    public function getAvatar() {
        return $this->avatar;
    }

    /**
     * @param  mixed $avatar
     * @param  string $adj 条件修饰符
     * @return  void
     */
    public function setAvatar($avatar, $adj = NULL) {
        $this->avatar = $avatar;
        $this->addCondition('avatar', $adj);
    }

    public function getAccounts() {
        // @TODO: 取登录帐号列表，即sys_user_account_index
        return UserModel::instance()->getAccounts($this->uid);
    }

    /**
     * 针对对象的明文密码进行加密
     *
     * @throws \Exception
     */
    public function hashPassword() {
        if (! $this->password) {
            throw new \Exception("密码不能为空");
        }

        $this->salt || $this->generateSalt();
        // @TODO: 调用统一的加密接口
    }

    /**
     * 生成随机的加密代码.
     *
     * @param int $len 长度
     */
    public function generateSalt($len = 6) {
        $this->salt = substr(md5(uniqid(rand(), true)), 0, $len);
    }

}