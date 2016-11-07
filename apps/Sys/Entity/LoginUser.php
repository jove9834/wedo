<?php
/**
 * Created by PhpStorm.
 * User: huangwj
 * Date: 2016/11/7
 * Time: 下午11:30
 */

namespace Apps\Sys\Entity;

/**
 * Class LoginUser
 *
 * 登录用户信息实体
 *
 * @package Apps\Sys\Entity
 */
class LoginUser
{
    /**
     * 用户ID
     *
     * @var integer
     */
    protected $uid;

    /**
     * 姓名
     *
     * @var string
     */
    protected $name;
    /**
     * 性别. 0 男， 1女
     *
     * @var integer
     */
    protected $gender;

    /**
     * 头像
     *
     * @var string
     */
    protected $avatar;

    /**
     * 用户类型. 1 系统管理员， 2 普通用户
     *
     * @var integer
     */
    protected $userType;

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return int
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @param int $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

}