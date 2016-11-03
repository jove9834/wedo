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
class UserProfileModel extends BaseModel {
    /**
     * 实体类名称
     *
     * @var string
     */
    protected $entityClass = 'Apps\Sys\Entity\UserProfile';

    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_user_profile';

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
    protected $uniqueColumn = NULL;

    /**
     * 根据用户ID获取用户信息
     *
     * @param integer $uid 用户ID
     * @return array
     */
    public function getProfile($uid) {
        $profile = $this->getAll(array('uid' => $uid), 'name, value', 'id')->result();
        $ret = array();
        if ($profile) {
            foreach ($profile as $item) {
                $ret[$item['name']] = $item['value'];
            }
        }

        return $ret;
    }

    /**
     * 设置用户属性
     *
     * @param integer $uid   用户ID
     * @param string  $name  属性名称
     * @param string  $value 属性值
     * @return boolean
     */
    public function setProfile($uid, $name, $value) {
        $profile = $this->get(array('uid' => $uid, 'name' => $name), 'id, value')->row();
        if ($profile) {
            // 已存在
            if ($value != $profile['value']) {
                // 更新
                return $this->update($profile['id'], array('value' => $value));
            }

            return TRUE;
        }
        else {
            // 不存在，添加
            return $this->add(array('uid' => $uid, 'name' => $name, 'value' => $value));
        }
    }

    /**
     * 取属性值
     *
     * @param integer $uid  用户ID
     * @param string  $name 属性名称
     * @return string|boolean 当不存在时，返回FALSE， 否则返回值
     */
    public function getValue($uid, $name) {
        $profile = $this->get(array('uid' => $uid, 'name' => $name), 'value')->row();
        if ($profile) {
            return $profile['value'];
        }

        return FALSE;
    }

    /**
     * 删除属性
     *
     * @param integer $uid  用户ID
     * @param string  $name 属性名称(选)
     * @return integer
     **/
    public function deleteProfile($uid, $name = NULL)
    {
        $where = array('uid' => $uid);
        if ($name) {
            $where['name'] = $name;
        }

        return $this->deleteByWhere($where);
    }

}