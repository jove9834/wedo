<?php
/**
 * 权限的API接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */

namespace Apps\Sys\Utils;

use Apps\Sys\Models\RoleModel;
use Apps\Sys\Models\RoleRightModel;
use Apps\Sys\Models\RoleItemModel;
use Apps\Sys\Models\MenuModel;

class RightApi {

    /**
     * 取一级角色
     *
     * @return array
     */
    public static function getTopRoles($cid = 0) {
        $roleModel = new RoleModel();
        return $roleModel->getTopRoles($cid);
    }

    /**
     * 取子角色
     *
     * @param integer $pid 上级角色ID
     * @return array
     */
    public static function getRoleChildren($pid) {
        $roleModel = new RoleModel();
        return $roleModel->getChildren($pid);        
    }
}