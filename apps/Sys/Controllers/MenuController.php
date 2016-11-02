<?php
/**
 * 菜单管理控制器
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
use Apps\Sys\Models\MenuModel;
use Apps\Sys\Models\MenuCategoryModel;
use Apps\Sys\Models\FunctionModel;
use Apps\Sys\Models\FunctionCategoryModel;
use Apps\Sys\Utils\MenuApi;

/**
 * 菜单管理控制器
 */
class MenuController extends BaseController {
    /**
     * 菜单实例
     *
     * @var MenuModel
     */
    private $menuModel;

    /**
     * 菜单分类实例
     *
     * @var MenuCategoryModel
     */
    private $menuCategoryModel;

    
    public function __construct(){
        parent::__construct();
        $this->menuModel = new MenuModel(); 
        $this->menuCategoryModel = new MenuCategoryModel(); 
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
        if (! $pid || $pid == '#') {
            $pid = 0;
        }

        $children = MenuApi::getMenuChildren($pid);        
        $this->jstreeRender($response, $children);
    }

    /**
     * 查询
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxSearch(AjaxResponse $response) {
        $keyword = wd_input('keyword');
        $data = $this->menuCategoryModel->getAll(array('c_lk_name' => $keyword));
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
        $functions = $this->menuModel->getAll(array('c_lk_title' => $keyword));
        foreach ($functions as $item) {
            $category_id = $item['category_id'];
            if (! $category_id) {
                continue;
            }

            $category = $this->menuCategoryModel->get($category_id);
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
     * 取分类下的菜单功能ID
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxMenuFunction(AjaxResponse $response) {
        $id = wd_input('id');        
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        $menus = $this->menuModel->getMenuByCategoryId($id);
        $result = array();
        foreach ($menus as $item) {
            $result[] = 'F' . $item['fun_id'];
        }

        $response->addData($result);
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
        
        $category = $this->menuCategoryModel->get($id);        
        if (! $category) {
            throw new ValidatedException("Error: 分类不存在！");
        }

        try {
            $this->menuCategoryModel->update($id, array('name' => $name));    
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
            $category = $this->menuCategoryModel->get($pid);        
            if (! $category) {
                throw new ValidatedException("Error: 上级分类不存在！");
            }
        }
        else {
            $pid = 0;
        }

        try {
            $data = array('pid' => $pid, 'name' => $name, 'display_order' => 0);
            $id = $this->menuCategoryModel->add($data);  
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
    public function ajaxAddMenu(AjaxResponse $response) {
        $category_id = wd_input('category_id');
        $fun_ids = wd_input('fun_ids');
        
        if (! $category_id || ! $fun_ids) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        $category = $this->menuCategoryModel->get($category_id);        
        if (! $category) {
            throw new ValidatedException("Error: 上级分类不存在！");
        }

        if (is_string($fun_ids)) {
            $fun_ids = explode(',', $fun_ids);
        }

        try {
            foreach ($fun_ids as $fun_id) {
                $data = array('category_id' => $category_id, 'fun_id' => $fun_id);
                $menu = $this->menuModel->get($data);
                if (! $menu) {
                    // 添加
                    $this->menuModel->add($data);
                }
            }

            $response->addAlert('添加菜单成功！');
            $response->addData();
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 删除菜单
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxRemoveMenu(AjaxResponse $response) {
        $category_id = wd_input('category_id');
        $fun_ids = wd_input('fun_ids');
        
        if (! $category_id || ! $fun_ids) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        $category = $this->menuCategoryModel->get($category_id);        
        if (! $category) {
            throw new ValidatedException("Error: 上级分类不存在！");
        }

        if (is_string($fun_ids)) {
            $fun_ids = explode(',', $fun_ids);
        }

        try {
            foreach ($fun_ids as $fun_id) {
                $data = array('category_id' => $category_id, 'fun_id' => $fun_id);
                $menu = $this->menuModel->deleteByWhere($data);                
            }

            $response->addAlert('菜单移除成功！');
            $response->addData();
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
        $name = wd_input('name');
        $url = wd_input('url');
        $title = wd_input('title');
        if (! $name || ! $url) {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        try {
            $data = array('title' => $title, 'url' => $url);
            $this->functionModel->update($name, $data);    
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
        $name = wd_input('fun_name');
        
        if (! $name) {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        try {
            $data = $this->functionModel->get($name);   
            // 获取功能权限项
            $rightItemModel = new RightItemModel();
            $rightItems = $rightItemModel->getFunctionRightItems($name);
            $data['items'] = $rightItems;
            $response->addData($data);
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
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        try {
            $this->menuCategoryModel->delete($id); 

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
                $this->menuCategoryModel->update($val, $data);
            }
            else {
                $data = array('category_id' => $pid, 'display_order' => $display_order);
                $this->menuModel->update($val, $data);
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
        $fun_name = wd_input('fun_name');
        $name = wd_input('name');
        $title = wd_input('title');
        $memo = wd_input('memo');

        if (! $fun_name || ! $name) {
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
                $data['fun_name'] = $fun_name;
                $response->addAlert('修改权限项成功！');
            }
            else {
                // 添加
                $data['fun_name'] = $fun_name;
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