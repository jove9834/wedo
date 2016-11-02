<?php
/**
 * 菜单的API接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */

namespace Apps\Sys\Utils;

use Apps\Sys\Models\MenuModel;
use Apps\Sys\Models\MenuCategoryModel;
use Apps\Sys\Models\FunctionModel;
use Apps\Sys\Models\RightItemModel;
use Wedo\Support\Tree;

/**
 * 菜单的API接口
 */
class Menu {
    /**
     * 取一级菜单
     *
     * @return array
     */
    public static function getTopMenus() {
        $menuCategoryModel = new MenuCategoryModel();
        return $menuCategoryModel->getChildren(0);
    }

    /**
     * 取子菜单
     *
     * @param integer $pid 上级ID
     * @return array
     */
    public static function getSubMenus($pid) {
        $menuCategoryModel = new MenuCategoryModel();
        return $menuCategoryModel->getChildren($pid);
    }    

    /**
     * 取菜单树
     *
     * @return array
     */
    public static function getMenuTree() {
        $menuCategoryModel = new MenuCategoryModel();
        $menuModel = new MenuModel();
        $functionModel = new FunctionModel();
        $functions = $functionModel->getAllFunction();
        $categories = $menuCategoryModel->getAll(NULL, '*', 'pid, display_order desc');
        $result = array();
        foreach ($categories as $category) {
            // 取分类下的菜单项
            $category_id = $category['id'];
            // 计算depth
            $arr = explode(',', $category['path']);
            $category['depth'] = count($arr);
            $category['isFunction'] = FALSE;
            $category['has_child'] = 1;
            $result[] = $category;

            $menus = $menuModel->getMenuByCategoryId($category_id);
            foreach ($menus as $menu) {
                $fun = $functions[$menu['fun_id']];
                $_fun = array();
                $_fun['id'] = 'M' . $menu['id'];
                $_fun['name'] = $fun['title'] == '' ? $fun['name'] : $fun['title'];
                $_fun['pid'] = $category_id;
                $_fun['type'] = $fun['type'];
                $_fun['url'] = $fun['type'] == 1 ? $fun['module'] . '/' . $fun['controller'] .'/' . $fun['action'] : $fun['url'];
                $_fun['has_child'] = 0;
                $_fun['isFunction'] = TRUE;
                $result[] = $_fun;
            }
        }

        $treeData = Tree::makeTree($result, 0, 'id', 'pid', 'name', 'li_attr');
        return $treeData;
    }

    /**
     * 取子菜单
     *
     * @param integer $pid 上级菜单ID
     * @return array
     */
    public static function getMenuChildren($pid) {
        $menuCategoryModel = new MenuCategoryModel();
        $functionModel = new FunctionModel();
        $functions = $functionModel->getAllFunction();
        $categories = $menuCategoryModel->getChildren($pid);
        $result = self::getFunctions($pid, $functions);
        foreach ($categories as $category) {
            // 取分类下的菜单项
            $category_id = $category['id'];
            // 计算depth
            $arr = explode(',', $category['path']);
            $category['depth'] = count($arr);
            $category['has_child'] = 1;
            $result[] = $category;

            $menus = self::getFunctions($category_id, $functions);
            $result = array_merge($result, $menus);            
        }

        $treeData = Tree::makeTree($result, $pid, 'id', 'pid', 'name', 'li_attr');        
        return $treeData;
    }

    protected static function getFunctions($category_id, $functions) {
        $result = array();
        $menuModel = new MenuModel();
        $menus = $menuModel->getMenuByCategoryId($category_id);
        foreach ($menus as $menu) {
            $fun = $functions[$menu['fun_id']];
            $_fun = array();
            $_fun['id'] = 'M' . $menu['id'];
            $_fun['name'] = $fun['title'] == '' ? $fun['name'] : $fun['title'];
            $_fun['pid'] = $category_id;
            $_fun['type'] = $fun['type'];
            $_fun['url'] = $fun['type'] == 1 ? $fun['module'] . '/' . $fun['controller'] .'/' . $fun['action'] : $fun['url'];
            $_fun['has_child'] = 0;
            $_fun['isFunction'] = TRUE;
            $result[] = $_fun;
        }

        return $result;
    }

    /**
     * 获取菜单ID
     *
     * @param integer $id 菜单ID
     * @return array
     */
    public static function getMenu($id) {
        $menuModel = new MenuModel();
        return $menuModel->get($id);
    }

    /**
     * 获取功能的所有权限项
     *
     * @param integer $pageId 页面ID
     * @return array
     */
    public static function getFunctionRightItem($function_id) {
        $rightItemModel = new RightItemModel();
        return $rightItemModel->getFunctionRightItems($function_id);
    }

    /**
     * 获取页面名称
     *
     * @param string $function_name 功能名称
     * @return array 返回功能信息数组
     */
    public static function getFunction($function_name) {
        $model = new FunctionModel();
        return $model->getFunction($function_name);
    }

    /**
     * 获取页面名称
     *
     * @param string $function_id 功能名称
     * @return array 返回功能信息数组
     */
    public static function getFunctionById($function_id) {
        $model = new FunctionModel();
        return $model->get($function_id);
    }
    
    /**
     * 自动收集功能信息
     *
     * @param string $module     模块名称
     * @param string $controller 控制器名称
     * @param string $action     Action
     * @return string 返回功能名称
     */
    public static function autoStoreFunction($module, $controller, $action = NULL) {
        $model = new FunctionModel();
        return $model->autoStore($module, $controller, $action);
    }
}