<?php
/**
 * 树构建类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Support;

/**
 * 树构建类
 */
class Tree {
    /**
     * 转换为树一级节点数组格式
     *
     * @param array  $item     数组
     * @param string $children 子结点数组
     * @param string $key_id   ID字段名称     
     * @param string $key_name 名称字段名称
     */
    public static function makeTreeNode($item, $children = array(), $key_id = 'id', $key_name = 'title', $attributeKey = 'attributes') {
        $hasChild = ! empty($children) || wd_array_val($item, 'has_child') == '1';
        $ret = array(
            'id'   => $item[$key_id],
            'text' => $item[$key_name],
        );

        if (array_key_exists('type', $item)) {
            $ret['type'] = $item['type'];
        }
        else {
            $ret['type'] = $hasChild ? 'folder' : 'item';
        }
 
        if ($children) {
            $ret['children'] = $children;
        }
        else if ($hasChild) {
            $ret['children'] = TRUE;
        }
        else {
            $ret['children'] = FALSE;   
        }        

        unset($item[$key_name], $item[$key_id]);
        // 属性
        $ret[$attributeKey] = $item;
        return $ret;
    }

    /**
     * 转换为树子节点数组格式
     *
     * @param array  $item     数组
     * @param string $key_id   ID字段名称     
     * @param string $key_name 名称字段名称
     * @return void
     */
    public static function makeChildTreeNode(array $items, $key_id = 'id', $key_name = 'title') {
        $node = array();
        foreach ($items as $item) {
            $data = array(
                'id'   => $item[$key_id],
                'text' => $item[$key_name],
                'type' => wd_array_val($item, 'has_child') == '1' ? 'folder' : 'item',                
            );

            unset($item[$key_name], $item[$key_id]);
            // 属性
            $data['attributes'] = $item;

            $node[] = $data;
        }

        return $node;
    }

    /**
     * 根据二维数组，遍历构造树型结构
     *
     * @param array  $items    二维数组
     * @param string $pid      上级ID
     * @param string $key_id   ID字段名称
     * @param string $key_pid  上级字段名称
     * @param string $key_name 名称字段名称
     * @return array
     */
    public static function makeTree(array $items, $pid = 0, $key_id = 'id', $key_pid = 'pid', $key_name = 'title', $attributeKey = 'attributes') {
        if (! $pid) {
            $pid = 0;
        }

        $treeData = array();
        foreach ($items as $i => $item) {
            if ($item[$key_pid] == $pid) {
                // 取子结点
                $children = self::makeTree($items, $item[$key_id], $key_id, $key_pid, $key_name, $attributeKey);
                $treeData[] = self::makeTreeNode($item, $children, $key_id, $key_name, $attributeKey);
            }
        }

        return $treeData;
    }
}