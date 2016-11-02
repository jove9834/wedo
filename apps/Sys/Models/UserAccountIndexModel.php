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

use Common\BaseModel;

/**
 * <<文件说明>>
 */
class UserAccountIndexModel extends BaseModel {
    /**
     * 实体类名称
     *
     * @var string
     */
    protected $entityClass = 'Apps\Sys\Entity\UserAccountIndex';

    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_user_account_index';

    /**
     * 表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 值唯一的字段
     *
     * @var string|array
     */
    protected $uniqueColumn = array('account');

    /**
     * 根据帐号获取用户ID
     *
     * 帐号指Email、用户名、手机号码等
     *
     * @param string $account 帐号，即Email、用户名、手机号码
     * @return integer|boolean 返回用户ID或FALSE未找到记录
     */
    public function getUserId($account) {
        if (! $account) {
            return FALSE;
        }

        $user = $this->get(array('account' => $account), 'uid')->row();
        return $user ? $user['uid'] : FALSE;
    }

    /**
     * 根据用户ID取用户的所有登录帐号
     *
     * @param $uid 用户ID
     * @return array|null
     */
    public function getUserAccounts($uid) {
        if (! $uid) {
            return NULL;
        }

        $uid = intval($uid);
        return $this->getAll(array('uid' => $uid))->entityResult();
    }

    /**
     * 判断帐号是否存在
     *
     * @param string $account 帐号
     * @return boolean
     */
    public function existsAccount($account) {
        if (! $account) {
            return FALSE;
        }

        $uid = $this->getUserId($account);
        return $uid !== FALSE;
    }

    /**
     * 添加帐号
     *
     * @param int     $uid     用户ID
     * @param string  $account 帐号
     * @param int     $type    类型
     * @throws \Exception 帐号不存在
     * @return integer 返回记录ID
     */
    public function addAccount($uid, $account, $type = 0) {
        if (! $account || ! $uid) {
            return FALSE;
        }

        // 判断帐号是否存在
        if ($this->existsAccount($account)) {
            throw new \Exception('帐号已存在！');
        }

        $data = array('account' => $account, 'uid' => $uid, 'type' => $type);
        return $this->add($data);
    }

    /**
     * 根据帐号名删除帐号
     *
     * @param string $account 帐号
     * @return integer|boolean 删除记录数或FALSE
     */
    public function deleteByAccount($account) {
        if (! $account) {
            return FALSE;
        }

        return $this->deleteByWhere(array('account' => $account));
    }

    /**
     * 根据用户ID删除帐号
     *
     * @param string $uid 用户ID
     * @return integer|boolean 删除记录数或FALSE
     */
    public function deleteByUid($uid) {
        if (! $uid) {
            return FALSE;
        }

        return $this->deleteByWhere(array('uid' => $uid));
    }
}