<?php
/**
 * 数据访问对象基类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Database;

use Exception;
use Wedo\Config;
use Wedo\InstanceTrait;
use Wedo\Logger;

/**
 * 数据访问对象基类
 */
class Model {
    use InstanceTrait;
    /**
     * 数据库连接实例
     *
     * @var Database
     */
    protected $db;
    
    /**
     * 指定数据连接名
     *
     * @var string
     */
    protected $connection = NULL;
    
    /**
     * 数据库表
     *
     * @var string
     */
    protected $table = NULL;

    /**
     * 表主键
     *
     * @var string|array
     */
    protected $primaryKey = 'id';

    /**
     * 列值唯一，不可重复
     *
     * @var string|array
     */
    protected $uniqueColumn = NULL;

    /**
     * 实体类名称
     *
     * @var string
     */
    protected $entityClass = NULL;

    /**
     * 单例
     *
     * @var Model
     */
//    private static $_models = array();

    /**
     * 表名前缀
     *
     * @return void
     */
    private $prefix = '';
    
    /**
     * 构造方法
     */
    public function __construct() {
        // 初始化数据连接实例
        $this->getDB();
    }

    /**
     * 静态调用方式，如：UserModel::instance()->get($userId)
     *
     * @return Model
     */
//    public static function instance() {
//        $c = get_called_class();
//        if (! isset(self::$_models[$c])) {
//            self::$_models[$c] = new static;
//        }
//
//        return self::$_models[$c];
//    }

    /**
     * 取数据库连接
     * 
     * @return Database
     */
    public function getDB() {
        if (!$this->db) {
            if ($this->connection) {
                $this->db = $this->connection($this->connection);
            } else {
                $this->db = $this->connection();
            }
        }
        
        return $this->db;
    }

    /**
     * 数据连接
     *
     * @param string $connection 连接名
     * @throws Exception
     * @return Database
     */
    public function connection($connection = NULL) {
        $config = Config::load('database', NULL, TRUE);
        if (! $config) {
            throw new Exception('没有找到数据库配置信息在config/database.php');
        }

        if ($connection) {
            $database = wd_array_val($config, $connection); 
            if (! $database) {
                throw new Exception('数据库连接配置 ' . $connection . ' 不存在！');
            }
        }
        else {
            $database = current($config);
        }

        $debug = wd_array_val($database, 'debug', FALSE);

        $conn = new Database($database['host'], $database['username'], $database['password'], $database['database']);
        $conn->setCharset($database['charset']);
        $this->prefix = wd_array_val($database, 'prefix');
        $debug && $conn->setDebug();
        return $conn;
    }
    
    /**
     * New 一个新的查询构造器
     * 
     * @return Query
     */
    public function createQuery() {
        return new Query($this);
    }

    /**
     * 转义字符串并加上单引号
     * 
     * @param string $str 字符串
     * @return string
     */
    public function quote($str)
    {
        if (is_string($str) || (is_object($str) && method_exists($str, '__toString'))) {
            return $this->db->quote($str);
        } elseif (is_bool($str)) {
            return ($str === FALSE) ? 0 : 1;
        } elseif ($str === NULL) {
            return 'NULL';
        }

        return $str;
    }

    /**
     * 检测列值是否已经存在
     * 
     * @param array   $data     更新的数据数组
     * @param integer $keyValue 主键值
     * @throws Exception 当唯一列为空时，抛出异常
     * @return boolean
     */
    public function _exists(array $data, $keyValue = NULL) {
        if ($this->uniqueColumn) {
            $query = $this->createQuery();
            if (is_array($this->uniqueColumn)) {
                foreach ($this->uniqueColumn as $column) {
                    $value = wd_array_val($data, $column);
                    if($value === FALSE){
                        if ($keyValue == NULL) {
                            throw new Exception(wd_print('列({})不能为空!', $column));
                        }
                        else {
                            return FALSE;
                        }                        
                    }

                    $query->where($column, $value);
                }
            }
            else {
                $value = wd_array_val($data, $this->uniqueColumn);
                if ($value === FALSE) {
                    if ($keyValue == NULL) {
                        throw new Exception(wd_print('列({})不能为空!', $this->uniqueColumn));
                    }
                    else {
                        return FALSE;
                    } 
                }

                $query->where($this->uniqueColumn, $value);
            }

            $obj = $query->get($this->table)->row();
            if ($obj) {
                if($keyValue && $obj[$this->primaryKey] == $keyValue) {
                    return FALSE;
                }

                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * 添加
     *
     * @param array $data 插入的数据 array(字段名=>值,字段名=>值)
     * @throws Exception 列值违返唯一性规则
     * @return mixed integer为插入的记录ID
     */
    public function add(array $data) {
        if ($this->_exists($data)) {
            // 已存在
            throw new Exception('列值违返唯一性规则！');
        }

        // 插入前触发勾子
        $this->beforeInsert($data);
        $query = $this->createQuery();
        $result = $query->insert($data);
        Logger::debug('data:{}', json_encode($data));
        if ($result) {
            if (! is_array($this->primaryKey)) {
                $id = wd_array_val($data, $this->primaryKey);
                if (! $id) {
                    $id = $query->getInsertId();
                }

                return $id;
            } else {
                return $this->getPrimaryKeyValue($data);
            }
        } else {
            throw new Exception('添加数据出错！');
        }
    }

    /**
     * 添加
     *
     * @param Entity $entity 要添加的实体
     * @throws Exception 列值违返唯一性规则
     * @return mixed integer为插入的记录ID
     */
    public function addEntity(Entity $entity) {
        if (! $entity) {
            throw new Exception('不能添加一个空实体');
        }

        return $this->add($entity->toArray(TRUE));
    }

    /**
     * 修改
     *
     * @param mixed $id   记录ID, 当主键为数组时，这个为数组
     * @param array $data 修改的数据 array(字段名=>值,字段名=>值)
     * @throws Exception 列值违返唯一性规则
     * @return integer 返回影响的行数
     */    
    public function update($id, array $data) {
        if (! $id) {
            throw new Exception("要更新ID不存在！");
        }

        if ($this->_exists($data, $id)) {
            // 已存在
            throw new Exception('列值违返唯一性规则！');
        }

        $old_data = $this->get($id)->row();
        if (! $old_data) {
            throw new Exception("要更新记录不存在！");
        }

        // 修改前触发勾子
        $this->beforeUpdate($id, $data, $old_data);

        $where = is_array($id) ? $id : array($this->primaryKey => $id);

        $result = $this->createQuery()->update($data, $where);
        if ($result) {
            // 触发更新后勾子
            $this->afterUpdate($id, $data, $old_data);
        }    

        return $result;
    }

    /**
     * 修改
     *
     * @param Entity $entity 修改的数据实体
     * @throws Exception 列值违返唯一性规则
     * @return integer 返回影响的行数
     */
    public function updateEntity(Entity $entity) {
        if (! $entity) {
            throw new Exception('不能更新一个空实体');
        }

        $data = $entity->toArray(TRUE);
        $id = $this->getPrimaryKeyValue($data);

        if (! $id) {
            throw new Exception('要修改的数据主键值不能为空');
        }

        return $this->update($id, $data);
    }

    /**
     * 更新符合条件的记录
     *
     * @param mixed $where 条件数组
     * @param array $data  修改的数据 array(字段名=>值,字段名=>值)
     * @return integer 返回更新的记录数
     */
    public function updateByWhere($where, array $data) {
        $items = $this->getAll($where)->result();
        $cn = 0;
        foreach ($items as $item) {
            $id = $this->getPrimaryKeyValue($item);
            $ret = $this->update($id, $data);
            $ret && $cn ++;
        }

        return $cn;
    }

    /**
     * 删除
     *
     * @param integer $id 记录ID
     * @throws Exception 要删除的ID不存在
     * @return boolean|integer TRUE为成功，integer为影响的记录数
     */
    public function delete($id) {
        if (! $id) {
            throw new Exception("要删除的ID不存在");
        }

        $old_data = $this->get($id)->row();
        if (! $old_data) {
            throw new Exception("要删除记录不存在！");
        }

        // 删除前触发勾子
        $this->beforeDelete($id, $old_data);

        $where = is_array($id) ? $id : array($this->primaryKey => $id);

        $result = $this->createQuery()->delete($where);
        if ($result) {
            $this->afterDelete($id, $old_data);
        }
        
        return $result;
    }

    /**
     * 删除
     *
     * @param Entity $entity 要删除的实体对象
     * @throws Exception
     * @return boolean|integer TRUE为成功，integer为影响的记录数
     */
    public function deleteEntity(Entity $entity) {
        if (! $entity) {
            throw new Exception('不能删除一个空实体');
        }

        $data = $entity->toArray(TRUE);
        $id = $this->getPrimaryKeyValue($data);

        if (! $id) {
            throw new Exception('要删除的数据主键值不能为空');
        }

        return $this->delete($id);
    }

    /**
     * 删除符合条件的记录
     *
     * @param mixed $where 条件数组
     * @return integer 返回删除的记录数
     */
    public function deleteByWhere($where) {
        $data = $this->getAll($where)->result();
        $cn = 0;
        foreach ($data as $item) {
            $id = $this->getPrimaryKeyValue($item);
            $this->delete($id);
            $cn ++;
        }

        return $cn;
    }

    /**
     * 取一条记录
     *
     * @param integer $id      数字型|数组array(column=>value)
     * @param string  $select  读取的字段
     * @param string  $orderBy 排序
     * @return Query
     */
    public function get($id, $select = '*', $orderBy = NULL) {
        $query = $this->createQuery();
        if (is_array($id)) {
            $query->where($id);
        } elseif ($id instanceof Entity) {
            $id->setQueryCondition($query);
        } else {
            $query->where($this->primaryKey, $id);
        }

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $query->select($select)->limit(0, 1)->get();
    }

    /**
     * 取符合条件的所有记录
     * 
     * @param mixed   $where    条件
     * @param string  $select   读取的字段
     * @param string  $orderBy  排序
     * @param integer $offset   偏移量
     * @param integer $pageSize 每页显示记录数
     * @return Query
     */
    public function getAll($where = NULL, $select = '*', $orderBy = NULL, $offset = NULL, $pageSize = 15) {
        $query = $this->createQuery();
        if ($where) {
            $where instanceof Entity ? $where->setQueryCondition($query) : $query->where($where);
        }

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        if ($offset !== NULL) {
            $query->limit($offset, $pageSize);
        }

        return $query->select($select)->get();
    }

    /**
     * 取符合条件的记录数
     * 
     * @param mixed  $where   条件
     * @return array 返回符合条件的记录
     */
    public function getRecordCount($where) {
        $query = $this->createQuery();
        if ($where) {
            $where instanceof Entity ? $where->setQueryCondition($query) : $query->where($where);
        }

        return $query->select('count(*)')->get()->getValue();
    }

    /**
     * 判断是否存在符合条件的记录
     *
     * @param mixed $where 条件
     * @return bool
     * @throws Exception
     */
    public function exists($where) {
        if (! $where) {
            throw new Exception("条件不能为空!");
        }

        $row = $this->get($where)->row();
        return ! is_null($row);
    }

    /**
     * 分页显示
     * 
     * @param mixed  $where    条件
     * @param string $order_by 排序
     * @param string $select   查询字段
     * @param int    $per_page 每页记录数
     * @return array (data, total)
     */
    public function pagination($where, $orderBy = NULL, $select = '*', $page = 1, $pagesize = 15) {
        $total = $this->getRecordCount($where);
        if (! $total) {
            return array(array(), 0);
        }

        $query = $this->createQuery();
        if ($where) {
            $where instanceof Entity ? $where->setQueryCondition($query) : $query->where($where);
        }

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        $data = $query->select($select)->page($page, $pagesize)->get()->result();
        
        return array($data, $total);
    }

    /**
     * 根据SQL查询
     *
     * @param string $sql 查询语句
     * @return array 返回符合条件的记录
     */
    public function queryBySql($sql) {
        return $this->createQuery()->simpleQuery($sql)->result();
    }

    /**
     * 执行更新的SQL语句
     * 
     * @param string $sql SQL语句
     * @return boolean|integer FALSE为运行失败,integer为SQL语句影响的记录数
     */
    public function execute($sql) {
        return $this->db->execute($sql);
    }
    
    /**
     * 获取当前模型的表名
     * 
     * @return string 返回表名
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * 获取主键，主键支持一个或多个
     *
     * @return string | array
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public function getEntityClass() {
        return $this->entityClass;
    }
    
    /**
     * 插入前触发事件
     * 
     * @param array &$data 插入数据
     * @return void
     */
    public function beforeInsert(array &$data) { }
    
    /**
     * 插入后触发事件
     * 
     * @param mixed $id   表主键值
     * @param array $data 插入的数据
     * @return void
     */
    public function afterInsert($id, array $data) { }
    
    /**
     * 更新前触发事件
     * 
     * @param mixed $id       表主键值
     * @param array &$data    修改的数据
     * @param array $old_data 修改前的数据
     * @return void
     */
    public function beforeUpdate($id, array &$data, array $old_data) { }
    
    /**
     * 更新后触发事件
     *
     * @param mixed $id       表主键值
     * @param array $data     修改的数据
     * @param array $old_data 修改前的数据
     * @return void
     */
    public function afterUpdate($id, array $data, array $old_data) { }
    
    /**
     * 删除前触发事件
     *
     * @param mixed $id       表主键值
     * @param array $old_data 删除前的数据
     * @return void
     */
    public function beforeDelete($id, array $old_data) { }
    
    /**
     * 删除后触发事件
     *
     * @param mixed $id       表主键值
     * @param array $old_data 删除前的数据
     * @return void
     */
    public function afterDelete($id, array $old_data) { }
    
    /**
     * 获取当前数据库连接名
     * 
     * @return string
     */
    public function getConnection(){
        return $this->connection;
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

    }

    /**
     * 获取主键值
     * @param array $data
     * @return array
     */
    private function getPrimaryKeyValue(array $data) {
        $ret = array();
        if (is_array($this->primaryKey)) {
            foreach ($this->primaryKey as $key) {
                $ret[$key] = wd_array_val($data, $key);
            }
        } else {
            $ret[$this->primaryKey] = wd_array_val($data, $this->primaryKey);
        }

        return $ret;
    }
}