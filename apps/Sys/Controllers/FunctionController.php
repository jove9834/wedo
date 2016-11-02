<?php
/**
 * 功能管理控制器
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Apps\Sys\Controllers;

use Common\BaseController;
use Common\AjaxResponse;
use Wedo\Core\Exception\ValidatedException;
use Wedo\Support\Tree;
use Apps\Sys\Models\FunctionModel;
use Apps\Sys\Models\RightItemModel;
use Apps\Sys\Models\FunctionCategoryModel;



class FunctionController extends BaseController {
    /**
     * FunctionModel
     *
     * @var FunctionModel
     */
    private $functionModel;

    /**
     * FunctionCategoryModel
     *
     * @var FunctionCategoryModel
     */
    private $functionCategoryModel;
    
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->functionModel = new FunctionModel(); 
        $this->functionCategoryModel = new FunctionCategoryModel(); 
    }
    
    /**
     * 列表页
     */
    public function indexAction() {
        return $this->display();
    }
    
    /**
     * 树型数据构造
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxLoadTree(AjaxResponse $response) {
        $pid = wd_input('id');
        if (!$pid || $pid == '#') {
            $pid = 0;
        }

        $children = $this->functionCategoryModel->getChildren($pid);
        if (! $children) {
            $children = [];
        }
        else {
            foreach ($children as &$category) {
                $category['has_child'] = 1;
                $category['isFunction'] = FALSE;
            }
        }

        // 取pid下的功能
        $functions = $this->functionModel->getFunctionByCategoryId($pid);
        foreach ($functions as $fun) {
            $_fun = array();
            $_fun['id'] = 'F' . $fun['id'];
            $_fun['name'] = $fun['title'] ?: $fun['name'];
            $_fun['pid'] = $pid;
            $_fun['has_child'] = 0;
            $_fun['isFunction'] = TRUE;
            $children[] = $_fun;
        }
        
        // 转换为Tree格式
        $treeData = Tree::makeTree($children, $pid, 'id', 'pid', 'name', 'li_attr');
        
        // $this->treeRender($response, 'function_category', $treeData);
        $this->jstreeRender($response, $treeData);
    }

    /**
     * 查询
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxSearch(AjaxResponse $response) {
        $keyword = wd_input('keyword');
        $data = $this->functionCategoryModel->getAll(array('c_lk_name' => $keyword));
        $ret = array();
        foreach ($data as $item) {
            $path = $item['path'];
            $paths = explode(',', $path);
            if ($paths) {
                $paths[0] = '#';
            }

            $ret[] = $paths;
        }

        // 查询功能
        $functions = $this->functionModel->getAll(array('c_lk_title' => $keyword));
        foreach ($functions as $item) {
            $category_id = $item['category_id'];
            if (! $category_id) {
                continue;
            }

            $category = $this->functionCategoryModel->get($category_id);
            if (! $category) {
                continue;
            }

            $path = $category['path'];
            $paths = explode(',', $path);
            if ($paths) {
                $paths[0] = '#';
            }

            $paths[] = $category['id'];
            $ret[] = $paths;
        }

        $response->addData($ret);
    }

    /**
     * 分类页面
     * 
     * @return view
     */
    public function categoryAction() {
        $id = wd_input('id');        
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        $category = $this->functionCategoryModel->get($id);        
        if (! $category) {
            throw new ValidatedException("Error: 分类不存在！");
        }

        $this->assign('category', $category);
        return $this->display();
    }
    
    /**
     * 分类更名
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxCategoryRename(AjaxResponse $response) {
        $id = wd_input('id');
        $name = wd_input('name');
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        $category = $this->functionCategoryModel->get($id);        
        if (! $category) {
            throw new ValidatedException("Error: 分类不存在！");
        }

        try {
            $this->functionCategoryModel->update($id, array('name' => $name));    
            $response->addResponse('reload-item');
            $response->addAlert('修改成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 添加子分类
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxAddCategory(AjaxResponse $response) {
        $pid = wd_input('pid');
        $name = wd_input('name');
        if ($pid == NULL) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        if ($pid) {
            $category = $this->functionCategoryModel->get($pid);        
            if (! $category) {
                throw new ValidatedException("Error: 上级分类不存在！");
            }
        }
        else {
            $pid = 0;
        }

        try {
            $data = array('pid' => $pid, 'name' => $name, 'display_order' => 0);
            $id = $this->functionCategoryModel->add($data);  
            $data['id'] = $id;  
            $response->addResponse('item', $data);
            $response->addAlert('添加分类成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 添加功能
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxAddFunction(AjaxResponse $response) {
        $category_id = wd_input('category_id');
        $type = wd_input('type');
        $controller = wd_input('controller');
        $module = wd_input('module');
        $action = wd_input('action');
        $url = wd_input('url');
        $title = wd_input('title');
        $name = wd_input('name');
        if ($type == 1) {
            $name = $this->functionModel->getFunctionName($module, $controller, $action);
        }

        if (! $title || ! $name) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        if ($category_id) {
            $category = $this->functionCategoryModel->get($category_id);        
            if (! $category) {
                throw new ValidatedException("Error: 上级分类不存在！");
            }
        }
        else {
            $category_id = 1; // 未分类
        }

        try {
            $data = array('category_id' => $category_id,
                'type' => $type, 
                'name' => $name, 
                'controller' => $controller, 
                'module' => $module, 
                'action' => $action, 
                'title' => $title, 
                'url' => $url);
            $id = $this->functionModel->add($data);
            $data['id'] = $id;
            $response->addResponse('item', $data);
            $response->addAlert('添加功能成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 更新功能信息
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxUpdateFunction(AjaxResponse $response) {
        $id = wd_input('id');
        $type = wd_input('type');
        $controller = wd_input('controller');
        $module = wd_input('module');
        $action = wd_input('action');
        $url = wd_input('url');
        $title = wd_input('title');
        $name = wd_input('name');

        if (! $id || ! $title) {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        if ($type == 1) {
            $name = $this->functionModel->getFunctionName($module, $controller, $action);
        }

        try {
            $data = array('type' => $type, 
                'controller' => $controller, 
                'module' => $module, 
                'action' => $action, 
                'title' => $title, 
                'name' => $name,
                'url' => $url);

            $this->functionModel->update($id, $data);    
            $response->addResponse('reload-item');
            $response->addAlert('修改功能成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 获取功能
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxGetFunction(AjaxResponse $response) {
        $id = wd_input('id');
        
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        try {
            $data = $this->functionModel->get($id);   
            // 获取功能权限项
            $rightItemModel = new RightItemModel();
            $rightItems = $rightItemModel->getFunctionRightItems($id);
            $response->addData(array('fun' => $data, 'items' => $rightItems));
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 删除单条记录
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxDelete(AjaxResponse $response) {
        $id = wd_input('id');
        $isFun = wd_input('is_fun');
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        try {
            if ((is_string($isFun) && $isFun == 'true') || (is_bool($isFun) && $isFun)) {
                $this->functionModel->delete($id);
            }
            else {
                $this->functionCategoryModel->delete($id);    
            }

            $response->addResponse('reload-item');
            $response->addAlert('删除成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 保存拖拽结果
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxSaveDrag(AjaxResponse $response) {
        $pid = wd_input('pid');
        $children = wd_input('children');
        if (! is_array($children)) {
            $children = explode(',', $children);
        }

        $count = count($children);
        $display_order = $count;
        foreach ($children as $val) {
            if (is_numeric($val)) {
                $data = array('display_order' => $display_order, 'pid' => $pid);
                $this->functionCategoryModel->update($val, $data);
            }
            else {
                $fun_id = substr($val, 1);
                $data = array('category_id' => $pid, 'display_order' => $display_order);
                $this->functionModel->update($fun_id, $data);
            }

            $display_order --;
        }

        $response->addData();
    }

    /**
     * 保存权限项，包含添加和修改功能
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxSaveRightItem(AjaxResponse $response) {
        $id = wd_input('id');
        $fun_id = wd_input('fun_id');
        $name = wd_input('name');
        $title = wd_input('title');
        $memo = wd_input('memo');

        if (! $fun_id || ! $name) {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        try {
            $rightItemModel = new RightItemModel();

            $data = array('name' => $name, 'title' => $title, 'memo' => $memo);
            if ($id) {
                // 修改
                $ri = $rightItemModel->get($id);
                if (! $ri) {
                    throw new ValidatedException("Error: 权限项不存在，修改失败！");
                }

                $rightItemModel->update($id, $data);
                $data['fun_id'] = $fun_id;
                $response->addAlert('修改权限项成功！');
            }
            else {
                // 添加
                $data['fun_id'] = $fun_id;
                $data['id'] = $rightItemModel->add($data);
                $response->addAlert('添加权限项成功！');
            }

            $response->addData($data);
            
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 删除权限项
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxDeleteRightItem(AjaxResponse $response) {
        $id = wd_input('id');
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        try {
            $rightItemModel = new RightItemModel();
            $rightItemModel->delete($id);

            $response->addResponse('reload-item');
            $response->addAlert('删除成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }
}