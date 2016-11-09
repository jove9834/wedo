<?php
/**
 * <<文件说明>>
 *
 * @author        黄文金
 * @copyright  Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link            http://weidu178.com
 * @since          Version 1.0
 */
namespace Apps\Sys\Models;

use Apps\Sys\Entity\User;
use Common\BaseModel;

/**
 * <<文件说明>>
 */
class UserModel extends BaseModel {
    /**
     * 实体类名称
     *
     * @var string
     */
    protected $entityClass = 'Apps\Sys\Entity\User';

    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_user';

    /**
     * 表主键
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * 值唯一的字段
     *
     * @var string|array
     */
    protected $uniqueColumn = NULL;

    /**
     * 根据用户ID获取用户实体对象
     *
     * @param integer $uid 用户ID
     * @return User
     */
    public function getUser($uid) {
        if (! $uid) {
            return NULL;
        }

        $uid = intval($uid);
        return $this->get($uid)->entity();
    }

    /**
     * 根据帐号获取用户信息
     *
     * 帐号指Email、用户名、手机号码等
     *
     * @param string $account 帐号，即Email、用户名、手机号码
     * @return User|NULL
     */
    public function getUserByAccount($account) {
        $uid = UserAccountIndexModel::instance()->getUserId($account);
        return $uid ? $this->getUser($uid) : NULL;
    }

    /**
     * 根据用户ID取用户的所有登录帐号
     *
     * @param integer $uid 用户ID
     * @return array|NULL
     */
    public function getUserAccounts($uid) {
        return UserAccountIndexModel::instance()->getUserAccounts($uid);
    }

    /**
     * 修改密码
     *
     * @param integer $uid 用户ID
     * @param string  $newPassword 新密码
     * @return integer
     * @throws \Exception
     */
    public function updatePassword($uid, $newPassword) {
        if (! $uid || ! $this->exists($uid)) {
            throw new \Exception("用户不存在!");
        }

        $user = new User($uid);
        $user->setPassword($newPassword);
        // 对密码进行加密
        $user->hashPassword();
        return $this->updateEntity($user);
    }

    /**
     * @param integer $uid     用户ID
     * @param string  $account 帐号名称
     * @param integer $type    类型
     * @return integer
     * @throws \Exception
     */
    public function addAccount($uid, $account, $type) {
        if (! $uid || ! $this->exists($uid)) {
            throw new \Exception("用户不存在!");
        }

        return UserAccountIndexModel::instance()->addAccount($uid, $account, $type);
    }

}