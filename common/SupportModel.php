<?php
/**
 * 运营支撑系统数据模型类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */

namespace Common;
use Wedo\Database\Model;

class SupportModel extends Model {
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
     * @var string|array
     */
    protected $writelog = FALSE;

    /**
     * 数据连接
     *
     * @param string $connection 连接名
     * @return Wedo\Database\Database
     */
    public function connection($connection = NULL) {
        $config = Config::load('database', NULL, TRUE);
        if (! $config) {
            throw new \Exception('没有找到数据库配置信息在config/database.php');
        }

        if ($connection) {
            $database = wd_array_val($config, $connection); 
            if (! $database) {
                throw new \Exception('数据库连接配置 ' . $connection . ' 不存在！');
            }
        }
        else {
            $database = current($config);
        }
        
        $conn = new Database($database['host'], $database['username'], $database['password'], $database['database']);
        $conn->setCharset($database['charset']);
        // $conn->setDebug();
        return $conn;
    }

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
     * @param string $operateType 操作类型
     * @param string $recordId    相关记录ID
     * @param array  $updateData  修改后内容
     * @param array  $srcData     修改前内容
     * @return void
     */
    protected function addOperateLog($operateType, $recordId, $updateData = NULL, $srcData = NULL) {
        $db = $this->connection();

        if($update_data && $old_data) {
            $dif_data = array();
            foreach ($update_data as $key => $value) {
                if($old_data[$key] != $value) {
                    $dif_data[$key] = $value;
                }
            }

            $update_data = $dif_data;
        }

        $data = array(
                'operate_type'  => $operateType,
                'table' => $this->table,
                'table_id'  => $recordId,
                'old_data'  => $srcData ? json_encode($srcData, JSON_UNESCAPED_UNICODE) : '',
                'update_data'   => $updateData ? json_encode($updateData, JSON_UNESCAPED_UNICODE) : '',
                'operate_time'  => time(),
                'connection'    => $this->connection,
        );

        if (defined('LOGIN_UID')) {
            $data['uid'] = LOGIN_UID;
        }

        if (defined('WD_OPERATE_NO')) {
            $data['operate_no'] = WD_OPERATE_NO;
        }

        return $db->insert('sys_table_log', $data);
    }
}