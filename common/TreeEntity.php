<?php
/**
 * <<文件说明>>
 *
 * @author        黄文金
 * @copyright  Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link            http://weidu178.com
 * @since          Version 1.0
 */
namespace Common;
use Wedo\Database\Entity;

class TreeEntity extends Entity
{

    protected $id;
    /**
     * 上级菜单ID
     *
     * @var integer
     */
    protected $pid;
    /**
     * 菜单路径，存放所有上级ID，以逗号分隔
     *
     * @var string
     */
    protected $path;

    /**
     * 是否有子菜单
     *
     * @var boolean
     */
    protected $hasChild;

    /**
     * get pid
     *
     * @return integer
     */
    public function getPid() {
        return $this->pid;
    }

    /**
     * @param mixed  $pid 上级菜单ID
     * @param string $adj 条件修饰符
     * @return $this
     */
    public function setPid($pid, $adj = NULL) {
        $this->pid = $pid;
        $this->addCondition('pid', $adj);
        return $this;
    }

    /**
     * get path
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * set path
     * @param mixed  $path 菜单路径
     * @param string $adj  条件修饰符
     * @return $this
     */
    public function setPath($path, $adj = NULL) {
        $this->path = $path;
        $this->addCondition('path', $adj);
        return $this;
    }

    /**
     * 是否有子菜单
     *
     * @return bool
     */
    public function getHasChild() {
        return $this->hasChild;
    }

    /**
     * set has child
     * @param mixed  $hasChild 是否有子菜菜单
     * @param string $adj      条件修饰符
     * @return  void
     */
    public function setHasChild($hasChild, $adj = NULL) {
        $this->hasChild = $hasChild;
        $this->addCondition('hasChild', $adj);
        return $this;
    }
}