<?php
/**
 * 数据模型类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */

namespace Common;
use Apps\Sys\Entity\LogData;
use Apps\Sys\Models\LogDataModel;
use Apps\Sys\Service\UserService;
use Common\Models\LogModel;
use Wedo\Database\Model;
use Wedo\Dispatcher;

class BaseModel extends Model {
    /**
     * 操作类型为添加常量
     *
     * @var string
     */
    const OPERATE_ADD = 'add';
    /**
     * 操作类型为修改常量
     *
     * @var string
     */
    const OPERATE_UPDATE = 'update';
    /**
     * 操作类型为删除常量
     *
     * @var string
     */
    const OPERATE_DELETE = 'delete';

    /**
     * 是否写日志
     *
     * @var boolean
     */
    protected $writelog = FALSE;

    /**
     * 插入后触发事件
     * 
     * @param integer $id   表主键值
     * @param array   $data 插入的数据
     * @return void
     */
    public function afterInsert($id, array $data) {
        parent::afterInsert($id, $data);
        // 写日志
        if ($this->writelog) {
            $this->addOperateLog(self::OPERATE_ADD, $id, $data);
        }
    }
        
    /**
     * 更新后触发事件
     *
     * @param integer $id       表主键值
     * @param array   $data     修改的数据
     * @param array   $old_data 修改前的数据
     * @return void
     */
    public function afterUpdate($id, array $data, array $old_data) {
        parent::afterUpdate($id, $data, $old_data);
        // 写日志
        if($this->writelog) {
            // 修改内容为比较修改前和修改后的差集
            $this->addOperateLog(self::OPERATE_UPDATE, $id, $data, $old_data);
        }
    }
    
    /**
     * 删除后触发事件
     *
     * @param integer $id       表主键值
     * @param array   $old_data 删除前的数据
     * @return void
     */
    public function afterDelete($id, array $old_data) {
        parent::afterDelete($id, $old_data);
        // 写日志
        if($this->writelog) {
            //修改内容为比较修改前和修改后的差集
            $this->addOperateLog(self::OPERATE_DELETE, $id, FALSE, $old_data);
        }
    }

    /**
     * 添加操作日志
     *
     * @param string $operateType   操作类型
     * @param string $primaryKeyVal 主键值
     * @param array  $afterData     修改后内容
     * @param array  $beforeData    修改前内容
     * @return mixed
     */
    protected function addOperateLog($operateType, $primaryKeyVal, $afterData = NULL, $beforeData = NULL) {
        if($afterData && $beforeData) {
            $dif_data = array();
            foreach ($afterData as $key => $value) {
                if($beforeData[$key] != $value) {
                    $dif_data[$key] = $value;
                }
            }

            $afterData = $dif_data;
        }

        $entity = LogData::create();
        $entity->setPrimaryKey($primaryKeyVal)
               ->setOperateType($operateType)
               ->setCreateAt(time())
               ->setTable($this->getTable())
               ->setConn($this->getConnection())
               ->setRequestUuid(Dispatcher::instance()->getRequest()->getUUID());

        $entity->setOldData($beforeData ? json_encode($beforeData, JSON_UNESCAPED_UNICODE) : '');
        $entity->setUpdateData($afterData ? json_encode($afterData, JSON_UNESCAPED_UNICODE) : '');
        if (UserService::isLogined()) {
            $entity->setUid(UserService::getLoginUid());
        }

        return LogDataModel::instance()->addEntity($entity);
    }

    /**
     * 获取操作日志记录
     *
     * @param mixed   $primaryKeyVal 主键值
     * @param integer $offset        offset
     * @param integer $pageSize      pagesize default 10
     * @return array
     */
    public function getOperateLogList($primaryKeyVal, $offset = NULL, $pageSize = 10) {
        $entity = LogData::create();
        $entity->setPrimaryKey($primaryKeyVal)->setTable($this->getTable());
        return LogModel::instance()->getAll($entity, '*', 'id desc', $offset, $pageSize)->entityResult();
    }
}