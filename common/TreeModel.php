<?php
/**
 * 树型结构数据库操作模型类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common;
use Wedo\Support\Tree;

/**
 * 树型结构模型基类
 */
class TreeModel extends BaseModel {
    /**
     * 插入前触发
     *
     * @param int  $id   插入的记录ID
     * @param array $data 插入数据内容
     * @return bool
     */
    public function beforeInsert(array &$data) {
        // 计算path
        if ($data['pid'] > 0) {
            // 取父节点
            $parent = $this->get($data['pid']);
            if(empty($parent)) {
                return FALSE;   
            }

            $path = trim($parent['path'], ',') . ',' . $parent['id'];
            if ($parent['has_child'] == '0') {
                // 更新为1
                $this->update($parent['id'], array('has_child' => 1));
            }
        }
        else {
            $path = '0';
        }

        $data['path'] = $path;

        return TRUE;
    }

    public function beforeUpdate($id, array &$newData, array $oldData) {        
        if (wd_array_val($newData, 'pid') === FALSE) {
            return TRUE;
        }

        // 重新计算path
        if ($newData['pid'] == $oldData['pid']) {
            return TRUE;
        }

        if ($newData['pid'] == '0') {
            $path = '0';
        }
        else {
            // 取父节点
            $parent = $this->get($newData['pid']);
            if(empty($parent)) {
                return FALSE;   
            }

            $path = trim($parent['path'], ',') . ',' . $parent['id'];
            if ($parent['has_child'] == '0') {
                // 更新为1
                $this->update($parent['id'], array('has_child' => 1));
            }
        }

        $newData['path'] = $path;        

        return TRUE;
    }

    /**
     * 更新后触发
     *
     * @param int   $id      更新的记录ID
     * @param array $newData 更新数据内容
     * @return bool
     */
    public function afterUpdate($id, array $newData, array $oldData) {
        if (wd_array_val($newData, 'pid') !== FALSE) {
            if ($oldData['pid'] != '0' && $newData['pid'] != $oldData['pid']) {
                // 判断父级节点是否还有子结点
                $children = $this->getAll(array('pid' => $oldData['pid']));
                if (! $children) {
                    $this->update($oldData['pid'], array('has_child' => 0));
                }
            }
        }

        if (wd_array_val($newData, 'path') !== FALSE && $newData['path'] != $oldData['path']) {
            $children = $this->getAll(array('pid' => $id));
            $path = $newData['path'] . ',' . $id;
            foreach ($children as $item) {
                if ($path != $item['path']) {
                    $this->update($item['id'], array('path' => $path));
                }
            }
        }

        return TRUE;
    }

    /**
     * 删除前触发
     *
     * @param $id 记录ID
     * @param array $data 删除记录的数据
     * @return bool
     */
    public function afterDelete($id, array $data) {
        if ($data['pid']) {
            // 重新计算path
            $parent = $this->get($data['pid']);
            if(empty($parent)) {
                return TRUE;   
            }

            // 判断父级节点是否还有子结点
            $children = $this->getAll(array('pid' => $parent['id']));
            if (! $children) {
                $this->update($parent['id'], array('has_child' => 0));
            }
        }

        return TRUE;
    }
}
