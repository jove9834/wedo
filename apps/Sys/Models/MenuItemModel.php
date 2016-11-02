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

use Apps\Sys\Entity\MenuItem;
use Common\BaseModel;

/**
 * <<文件说明>>
 */
class MenuItemModel extends BaseModel {
    /**
     * 实体类名称
     *
     * @var string
     */
    protected $entityClass = 'Apps\Sys\Entity\MenuItem';

    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_menu_item';

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
     * 根据ID获取菜单项
     *
     * @param integer $id 菜单项ID
     * @return MenuItem | NULL;
     */
    public function getMenuItem($id) {
        if (! $id) {
            return NULL;
        }

        $id = intval($id);
        return $this->get($id)->entity();
    }

}