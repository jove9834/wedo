<?php
/**
 * 数据模型基类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Database;

use Exception;

/**
 * 查询构造类
 */
class Query {
    /**
     * 数据库操作实例
     *
     * @var Model
     */
    private $model;
    /**
     * select 字段
     *
     * @var string
     */
    private $selectField = '*';
    /**
     * joins
     *
     * @var array
     */
    private $joins = array();
    /**
     * where
     *
     * @var array
     */
    private $wheres = array();
    /**
     * order by
     *
     * @var array
     */
    private $orderBys = array();
    /**
     * offset
     *
     * @var boolean|integer
     */
    private $offsetNum = FALSE;
    /**
     * limit
     *
     * @var boolean|integer
     */
    private $limitNum = FALSE;

    /**
     * 修饰符
     *
     * @var array
     */
    protected static $sysadj = array('IN', 'LK','LKA', 'LKB', 'NIN', 'EQ', 'NE', 'LT', 'LE', 'GT', 'GE', 'BTW');

    /**
     * 构造函数
     *
     * @param mixed $model 数据库模型
     */
    public function __construct($model = NULL)
    {
        $this->model = $model;            
    }

    /**
     * 根据参数设置查询范围
     *
     * @param array  $parameters 条件数组 格式如：array('c_lk_username'=>'a')表示用户名含有a的记录
     * @param string $orderBy    默认排序字段名称 
     * @param string $direction  默认排序方式，asc|desc 
     * @return Query
     */
    public function setParameters(array $parameters, $orderBy = NULL, $direction = 'asc')
    {
        $sort = FALSE;
        if ($parameters) {
            foreach ($parameters as $var => $val) {
                $var = trim($var);
                $val = trim($val);
                if (empty($val) && $val !== '0') {
                    continue;
                }
                if (strlen($var) > 2 && strtoupper(substr($var, 0, 2)) != 'C_') {
                    if ($var == 'sort_up') {
                        $this->orderBy($val);
                        $sort = TRUE;
                    } elseif ($var == 'sort_down') {
                        $this->orderBy($val, 'DESC');
                        $sort = TRUE;
                    }
                    continue;
                }
                $this->parseWhere($var, $val);
            }
        }

        // 设置默认排序
        if(!$sort && is_null($orderBy)){
             $this->orderBy($orderBy, $direction);
        }
        
        return $this;
    }

    /**
     * 解析查询条件
     * 
     * @param string $column 格式为：c_修饰符_字段名
     * @param mixed  $val    字段值
     * @param integer $index 二维条件数组顺序ID
     * @return void
     */
    private function parseWhere($column, $val, $index = 0) {
        if(strlen($column) < 2 || strtoupper(substr($column, 0, 2)) != 'C_'){
            $this->where($column, $val, 'EQ', $index);
            // $this->wheres[] = array('column' => $column, 'operator' => 'EQ', 'val' => $val); 
            return;
        }

        
        $_column = substr($column, 2);
            
        // 筛选修饰符参数IN_|LK_|LKA_|LKB_|NIN_|EQ_|NE_|LT_|LE_|GT_|GE_
        if (preg_match('/^(IN|LK|LKA|LKB|NIN|EQ|NE|LT|LE|GT|GE|BTW|DBTW)_(.+)/i', $_column, $match)) { 
            $_pre = strtoupper($match[1]);
            $name = $match[2];
            $_adj = '';
            if (in_array($_pre, self::$sysadj)) {
                $_adj = $_pre;
            }

            $this->where($name, $val, $_adj, $index);
            // $this->wheres[] = array('column' => $name, 'operator' => $_adj, 'val' => $val);    
        }
        else {
            $this->where($column, $val, 'EQ', $index);
            // $this->wheres[] = array('column' => $column, 'operator' => 'EQ', 'val' => $val); 
        }
    }

    /**
     * 解析查询修饰符
     *
     * @param string  $column   字段名称
     * @param string  $val      字段值
     * @param string  $operator 修饰符（操作符）
     * @param integer $index    二维条件数组顺序ID
     * @return Query
     */
    public function where($column, $val = NULL, $operator = NULL, $index = 0) {
        if (!$column) {
            return $this;
        }

        if (is_array($column)) {
            $_index = -1;
            foreach ($column as $key => $val) {
                if (is_array($val) && (is_int($key) || strtolower($key) == 'or')) {
                    $_index = $_index + 1;
                    $this->where($val, NULL, NULL, $_index);
                }
                else {
                    $this->where($key, $val, NULL, $index);
                }               
            }
        }
        else {
            if ($operator == NULL) {
                $this->parseWhere($column, $val, $index);
            }
            else {
                if ($index < 0) {
                    $index = 0;
                }

                if (! isset($this->wheres[$index])) {
                    $this->wheres[$index] = array();
                }

                $this->wheres[$index][] = array('column' => $column, 'operator' => $operator, 'val' => $val);
            }              
        }

        return $this;
    }

    /**
     * 排序
     * 
     * @param string|array $column    排序列
     * @param string       $direction 排序方向，ASC, DESC
     * @return Query
     */
    public function orderBy($column, $direction = NULL) {
        if (!$column) {
            return $this;
        }

        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->orderBy($key, $val);
            }
        }
        else {
            $direction = $direction ? $direction : 'ASC';
            $this->orderBys[] = array('column' => $column, 'direction' => $direction);
        }
        
        return $this;
    }

    /**
     * join
     * 
     * @param string       $table join表名
     * @param string|array $where 关联条件
     * @param string       $adj   修饰符
     * @return Query
     */
    public function join($table, $where, $adj = 'INNER') {
        $adj = $adj ? $adj : 'INNER';
        $this->joins[] = array('table' => $table, 'where' => $where, 'adj' => $adj);
        return $this;
    }

    /**
     * 查询字段
     *
     * @param mixed $select 查询字段
     * @return Query
     */
    public function select($select = '*') {
        $select = $select ? $select : '*';
        $this->selectField = is_array($select) ? implode(',', $select) : $select;
        return $this;
    }

    /**
     * 查询限制
     *
     * @param integer $offset 偏移量
     * @param integer $limit  限制记录数
     * @return Query
     */
    public function limit($offset, $limit) {
        $this->offsetNum = $offset;
        $this->limitNum = $limit;
        return $this;
    }

    /**
     * 分页计算
     *
     * @param integer $page    当前页码
     * @param integer $perpage 每页记录数
     * @return Query
     */
    public function page($page = 0, $perpage = 20) {
        $page = intval($page) - 1;
        $page = $page <= 0 ? 0 : $page;

        $perpage = intval($perpage);
        $perpage = $perpage <= 0 ? 20 : $perpage;

        $offset = $page * $perpage;
        $this->limit($offset, $perpage);
        return $this;
    }

    /**
     * 取查询条件
     *
     * @return string 返回查询条件
     */
    public function getWhere() {
        $whereStr = '';
        
        foreach ($this->wheres as $index => $wheres) {
            $subWhere = NULL;
            foreach ($wheres as $where) {
                if ($subWhere) {
                    $subWhere .= ' AND ';
                }

                $subWhere .= $this->composeTheWhereString($where);
            }

            if ($subWhere) {
                if ($whereStr) {
                    $whereStr .= ' OR ';
                }

                $whereStr .= '(' . $subWhere . ')';
            }
        }

        return $whereStr;
    }

    private function composeTheWhereString($where) {
        $whereStr = '';
        switch ($where['operator']) {
            case 'IN':
                $values = array();
                if (is_array($where['val'])) {
                    $vals = $where['val'];
                }
                else {
                    $vals = explode(',', $where['val']);
                }

                foreach ($vals as $value) {
                    $values[] = $this->model->quote($value);
                }

                $whereStr = $where['column'] . ' IN (' . implode(',', $values) . ')';
                break;
            case 'LK':
                $whereStr = $where['column'] . ' LIKE ' . $this->model->quote('%' . $where['val'] . '%');
                break;
            case 'LKA':
                $whereStr = $where['column'] . ' LIKE ' . $this->model->quote('%' . $where['val']);
                break;
            case 'LKB':
                $whereStr = $where['column'] . ' LIKE ' . $this->model->quote($where['val'] . '%');
                break;
            case 'NIN':
                $values = array();
                if (is_array($where['val'])) {
                    $vals = $where['val'];
                }
                else {
                    $vals = explode(',', $where['val']);
                }

                foreach ($vals as $value) {
                    $values[] = $this->model->quote($value);
                }
                
                $whereStr = $where['column'] . ' NOT IN (' . implode(',', $values) . ')';
                break;
            case 'NE':
                $whereStr = $where['column'] . ' != ' . $this->model->quote($where['val']);
                break;
            case 'LT':
                $whereStr = $where['column'] . ' < ' . $this->model->quote($where['val']);
                break;
            case 'LE':
                $whereStr = $where['column'] . ' <= ' . $this->model->quote($where['val']);
                break;
            case 'GT':
                $whereStr = $where['column'] . ' > ' . $this->model->quote($where['val']);
                break;
            case 'GE':
                $whereStr = $where['column'] . ' >= ' . $this->model->quote($where['val']);
                break;
            case 'BTW': // between
                $values = explode(' - ', $where['val']);
                if (count($values) == 2) {
                    // foreach ($where['val'] as $value) {
                    //     $values = $this->model->quote($value);
                    // }
                    $whereStr = $where['column'] . ' BETWEEN ' . $this->model->quote($values[0]) . ' AND ' . $this->model->quote($values[1]);
                }

                break;
            case 'DBTW': // Datetime 转为时间长整型
                $values = explode(' - ', $where['val']);
                if(count($values) != 2){
                    break;
                }

                $bgn_time = $values[0];
                $end_time = $values[1];
                
                if(strlen($end_time) <= 10){
                    $end_time .= ' 23:59:59';
                }

                $whereStr = $where['column'] . ' BETWEEN ' . $this->model->quote(strtotime($bgn_time)) . ' AND ' . $this->model->quote(strtotime($end_time));
                break;
            default:
                if (!is_null($where['val'])) {
                    $whereStr = $where['column'] . ' = ' . $this->model->quote($where['val']);
                }
                else {
                    $whereStr = $where['column'];
                }                    
        }

        return $whereStr;
    }

    /**
     * 取排序语句
     *
     * @return string 返回排序语句
     */
    public function getOrderBy() {
        $orderStr = '';
        foreach ($this->orderBys as $order) {
            $orderStr .= ',' . $order['column'] . ' ' . $order['direction'];
        }

        $orderStr = ($orderStr != '') ? substr($orderStr, 1) : '';
        return $orderStr;
    }

    /**
     * Join
     *
     * @return string
     */
    public function getJoin() {
        $join_sql = array();
        foreach ($this->joins as $join) {
            $join_sql[] = wd_print('{} JOIN {} ON {}', $join['adj'], $join['table'], $join['where']);
        }

        return implode(" ", $join_sql);
    }

    /**
     * 取查询字段
     *
     * @return string
     */
    public function getSelect() {
        return $this->selectField;
    }

    /**
     * 取查询limit
     *
     * @return string
     */
    public function getLimit() {
        $limit = '';
        if ($this->offsetNum || $this->limitNum) {
            if ($this->offsetNum) {
                $limit = $this->offsetNum;
            }

            if ($this->limitNum) {
                $limit = ($limit == '') ? '0,' . $this->limitNum : $limit . ',' . $this->limitNum;
            }

            $limit = ' LIMIT ' . $limit;    
        }

        return $limit;
    }

    /**
     * 重置查询参数
     *
     * @return Query
     */
    public function reset() {
        $this->wheres = array();
        $this->orderBys = array();
        $this->selectField = '*';
        $this->joins = array();
        return $this;
    }

    /**
     * 根据条件查询记录
     *
     * @param mixed $where 查询条件
     * @return Query
     */
    public function get($where = NULL) {
        if (!$where) {
            $this->where($where);
        }

        $sql = wd_print("SELECT {} FROM {} t", $this->getSelect(), $this->model->getTable());

        if ($join = $this->getJoin()) {
            $sql .= " " . $join;
        }

        if ($where = $this->getWhere()) {
            $sql .= " WHERE " . $where;
        }

        if ($order_by = $this->getOrderBy()) {
            $sql .= " ORDER BY " . $order_by;
        }

        $sql .= $this->getLimit();
        $this->model->getDB()->query($sql);
        return $this;
    }

    /**
     * 更新
     *
     * @param mixed  $data  更新数据
     * @param mixed  $where 条件
     * @return boolean|integer
     */
    public function update($data, $where = NULL) {
        $where && $this->where($where);
        $whereStr = $this->getWhere();
        return $this->model->getDB()->update($this->model->getTable(), $data, $whereStr);
    }

    /**
     * 插入
     *
     * @param mixed  $data  数据
     * @return boolean|integer
     */
    public function insert($data) {
        return $this->model->getDB()->insert($this->model->getTable(), $data);
    }

    /**
     * 取插入后递增主键值
     *
     * @return integer
     */
    public function getInsertId(){
        return $this->model->getDB()->getInsertId();
    }

    /**
     * 删除
     *
     * @param mixed  $where 条件
     * @return boolean|integer
     */
    public function delete($where = NULL) {
        if (!empty($where)) {
            $this->where($where);
        }

        $whereStr = $this->getWhere();

        return $this->model->getDB()->delete($this->model->getTable(), $whereStr);
    }

    /**
     * 取查询结果集的值
     *
     * @param integer $offset 偏移量
     * @return mixed
     */
    public function getValue($offset = 0) {
        $row = $this->row(MYSQL_NUM);
        if(isset($row[$offset])){
            return $row[$offset];
        }

        return NULL;
    }

    /**
     * 根据SQL查询
     *
     * @param string $sql 查询语句
     * @return Query
     */
    public function simpleQuery($sql) {
        $this->model->getDB()->query($sql);
        return $this;
    }

    /**
     * 取一条查询结果
     *
     * @param integer $type 类型
     * @return array
     */
    public function row($type = MYSQL_ASSOC) {
        return $this->model->getDB()->fetchRow($type);
    }

    /**
     * 取查询结果集
     *
     * @param integer $type       类型
     * @return array
     */
    public function result($type = MYSQL_ASSOC) {
        $primaryKey = is_array($this->model->getPrimaryKey()) ? implode(',', $this->model->getPrimaryKey()): $this->model->getPrimaryKey();
        return $this->model->getDB()->fetchAll($primaryKey, $type);
    }

    /**
     * 返回一个实体对象
     *
     * @return Entity
     */
    public function entity() {
        $row = $this->row();
        return $this->arrayToEntity($row);
    }

    /**
     * 返回实体结果集
     *
     * @return array
     */
    public function entityResult() {
        $result = array();
        $data = $this->result();
        foreach ($data as $row) {
            $result[] = $this->arrayToEntity($row);
        }

        return $result;
    }

    /**
     * 数组转换为实体对象
     *
     * @param array $row 数组
     * @return Entity|NULL
     * @throws Exception
     */
    private function arrayToEntity(array $row) {
        if (! $row) {
            return NULL;
        }

        $entityClass = $this->model->getEntityClass();
        if (! $entityClass) {
            throw new Exception(wd_print("模型未定义实体类，不能返回实体对象！", get_class($this->model)));
        }
        $entity = new $entityClass;
        return $entity->fromArray($row);
    }

    /**
     * 开始事务
     *
     * @return boolean
     */
    public function transStart() {
        $this->model->getDB()->query('SET AUTOCOMMIT=0');
        $this->model->getDB()->query('START TRANSACTION'); // can also be BEGIN or BEGIN WORK
        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * 事务提交
     *
     * @return boolean
     */
    public function transCommit() {
        $this->model->getDB()->query('COMMIT');
        $this->model->getDB()->query('SET AUTOCOMMIT=1');
        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * 事务回滚
     *
     * @return boolean
     */
    public function transRollback() {
        $this->model->getDB()->query('ROLLBACK');
        $this->model->getDB()->query('SET AUTOCOMMIT=1');
        return TRUE;
    }
}