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

/**
 * 树型结构模型基类
 */
class TreeModel extends BaseModel {
    /**
     * 插入前触发
     *
     * @param array $data 插入数据内容
     * @return void
     */
    public function beforeInsert(array &$data) {
        // 计算path
        if ($data['pid'] > 0) {
            // 取父节点
            $parent = $this->get($data['pid'])->row();
            if(empty($parent)) {
                parent::beforeInsert($data);
                return;
            }

            $path = trim($parent['path'], ',') . ',' . $parent['id'];
            if ($parent['has_child'] == '0') {
                // 更新为1
                $this->update($parent['id'], array('has_child' => 1));
            }
        } else {
            $path = '0';
        }

        $data['path'] = $path;
        parent::beforeInsert($data);
    }

    /**
     * 更新前计算path
     *
     * @param mixed $id
     * @param array $newData
     * @param array $oldData
     */
    public function beforeUpdate($id, array &$newData, array $oldData) {        
        if (wd_array_val($newData, 'pid') === FALSE) {
            parent::beforeUpdate($id, $newData, $oldData);
            return;
        }

        // 重新计算path
        if ($newData['pid'] == $oldData['pid']) {
            parent::beforeUpdate($id, $newData, $oldData);
            return;
        }

        if ($newData['pid'] == '0') {
            $path = '0';
        } else {
            // 取父节点
            $parent = $this->get($newData['pid'])->row();
            if(empty($parent)) {
                parent::beforeUpdate($id, $newData, $oldData);
                return;
            }

            $path = trim($parent['path'], ',') . ',' . $parent['id'];
            if ($parent['has_child'] == '0') {
                // 更新为1
                $this->update($parent['id'], array('has_child' => 1));
            }
        }

        $newData['path'] = $path;
        parent::beforeUpdate($id, $newData, $oldData);
    }

    /**
     * 更新后触发
     *
     * @param mixed $id 更新的记录ID
     * @param array $newData 更新数据内容
     * @param array $oldData
     */
    public function afterUpdate($id, array $newData, array $oldData) {
        if (wd_array_val($newData, 'pid') !== FALSE) {
            if ($oldData['pid'] != '0' && $newData['pid'] != $oldData['pid']) {
                // 判断父级节点是否还有子结点
                if (! $this->hasChildren($oldData['pid'])) {
                    $this->update($oldData['pid'], array('has_child' => 0));
                }
            }
        }

        if (wd_array_val($newData, 'path') !== FALSE && $newData['path'] != $oldData['path']) {
            $children = $this->getAll(array('pid' => $id))->result();
            $path = $newData['path'] . ',' . $id;
            foreach ($children as $item) {
                if ($path != $item['path']) {
                    $this->update($item['id'], array('path' => $path));
                }
            }
        }

        parent::afterUpdate($id, $newData, $oldData);
    }

    /**
     * 删除前触发
     *
     * @param mixed $id   记录ID
     * @param array $data 删除记录的数据
     * @return bool
     */
    public function afterDelete($id, array $data) {
        if ($data['pid']) {
            // 重新计算path
            $parent = $this->get($data['pid'])->row();
            if(empty($parent)) {
                return;
            }

            // 判断父级节点是否还有子结点
            if (! $this->hasChildren($parent['id'])) {
                $this->update($parent['id'], array('has_child' => 0));
            }
        }
    }

    /**
     * 获取指定上级ID下的所有子结点
     *
     * @param integer $parentId 上级ID
     * @param string  $orderBy  排序，默认不排序
     * @return array 返回实体对象数组
     */
    public function getChildren($parentId = 0, $orderBy = NULL) {
        $parentId = intval($parentId);
        return $this->getAll(array('pid' => $parentId), '*', $orderBy)->entityResult();
    }

    /**
     * 是否有子结点
     *
     * @param integer $id 节点ID
     * @return boolean TRUE为有子节点，FALSE没有子节点
     */
    public function hasChildren($id) {
        if (! $id) {
            return FALSE;
        }

        $id = intval($id);
        $child = $this->get(array('pid' => $id))->row();
        return ! empty($child);
    }
}
