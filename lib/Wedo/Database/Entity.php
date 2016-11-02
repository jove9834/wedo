<?php
/**
 * 实体类, 所有的数据实体都继续该类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Database;

use Wedo\Application;
use Wedo\Config;
use Wedo\Logger;

/**
 * 实体类, 实体定义的类变量只能是对应表的字段名
 *
 * 变量名称符合首字母小写的 Camel caps format
 * 如：user_name 则类变量名为 userName 
 */
class Entity {
    /**
     * 修饰符
     *
     * @var array
     */
//    protected static $sysadj = array('IN', 'LK','LKA', 'LKB', 'NIN', 'EQ', 'NE', 'LT', 'LE', 'GT', 'GE', 'BTW');

    /**
     * 查询条件
     *
     * @var string
     */
    protected $_conditions = array();

    /**
     * 魔术方法，设置属性值
     *
     * @param string $name  属性名称
     * @param mixed  $value 属性值
     * @return void
     */
    public function __set($name, $value){
        $vars = wd_object_vars($this);
        if (in_array($name, $vars)) {
            $this->$name = $value;
        }
    } 
    
    /**
     * 魔术方法，取属性值
     *
     * @param string $name 属性名称
     * @return mixed
     */
    public function __get($name){
        if (! isset($this->$name)) {
            return NULL;
        }

        return $this->$name;     
    }

    public function addCondition($fieldName, $sysadj = 'EQ') {
        $this->_conditions[$fieldName] = $sysadj;
    }

    public function getConditionAdj($fieldName) {
        return wd_array_val($this->_conditions, $fieldName, 'EQ');
    }

    public function removeCondition($fieldName) {
        unset($this->_conditions[$fieldName]);
    }



    public function toArray($filterNull = FALSE) {
        $vars = get_object_vars($this);
        $data = array();
        foreach ($vars as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }

            if ($filterNull && is_null($value)) {
                continue;
            }

            // 对key进行un
            $fieldName = wd_uncamel_case($key);
            $data[$fieldName] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    public function fromArray($arr) {
        foreach ($arr as $key => $value) {
            $name = wd_camel_case($key);
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * 将实体对象做为查询条件
     *
     * @param Query $query 查询实例
     */
    public function setQueryCondition(Query $query) {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (substr($key, 0, 1) == '_' || is_null($value)) {
                continue;
            }

            $adj = $this->getConditionAdj($key);
            // 对key进行uncamel case
            $fieldName = wd_uncamel_case($key);
            $value = is_string($value) ? trim($value) : $value;
            $query->where($fieldName, $value, $adj);
        }
    }
}