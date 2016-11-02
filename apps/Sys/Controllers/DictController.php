<?php
/**
 * 字典管理控制器
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Apps\Sys\Controllers;

use Wedo\Core\Exception\ValidatedException;
use Common\BaseController;
use Common\AjaxResponse;
use Apps\Sys\Models\DictModel;
use Apps\Sys\Models\DictItemModel;


/**
 * 字典管理控制器
 */
class DictController extends BaseController {
    /**
     * DictModel
     *
     * @var DictModel
     */
    private $dictModel;

    /**
     * DictItemModel
     *
     * @var DictItemModel
     */
    private $dictItemModel;
    
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->dictModel = DictModel::instance(); 
        $this->dictItemModel = DictItemModel::instance(); 
    }
    
    /**
     * 列表页
     */
    public function indexAction() {
        return $this->display();
    }

    /**
     * 数据查询
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxSearch(AjaxResponse $response) {
        $page = wd_input('page', 1);
        $pagesize = wd_input('ps', 15);
        $keyword = wd_input('keyword');
        $query = NULL;
        if ($keyword) {
            $query = array(array('c_lk_name' => $keyword), array('c_lk_title' => $keyword));
        }

        list($data, $total) = $this->dictModel->pagination($query, 'id desc', '*', $page, $pagesize);
        $response->addData(array('page' => $page, 'total' => $total, 'data' => $data));
    }

    /**
     * 获取字典信息
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxGetDict(AjaxResponse $response) {
        $id = wd_input('id');
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        try {
            $data = $this->dictModel->get($id);
            // 获取字典项
            $items = $this->dictItemModel->getDictItems($id);
            // foreach ($items as &$item) {
            //     $item['writable'] = FALSE;
            // }

            $response->addData(array('dict' => $data, 'items' => $items));
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 保存字典信息
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxSaveDict(AjaxResponse $response) {
        $id = wd_input('id');
        $name = wd_input('name');
        $module = wd_input('module');
        $title = wd_input('title');
        if ($name === NULL || $name === FALSE  || $name === '' || ! $title) {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        try {
            $data = array('title' => $title, 'name' => $name, 'module' => $module);
            if ($id) {
                $this->dictModel->update($id, $data);
                $response->addAlert('修改字典成功！');
            }
            else {
                $id = $this->dictModel->add($data);
                $response->addAlert('添加字典成功！');
            }

            $data['id'] = $id;
            $response->addData($data);
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 保存字典项信息
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxSaveItem(AjaxResponse $response) {
        $id = wd_input('id');
        $dict_id = wd_input('dict_id');
        $title = wd_input('title');
        $value = wd_input('value');
        $display_order = wd_input('display_order');
        if (! $title || $value === NULL || $value === FALSE  || $value === '') {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        try {
            $data = array('title' => $title, 'dict_id' => $dict_id, 'value' => $value, 'display_order' => $display_order);
            if ($id) {
                $this->dictItemModel->update($id, $data);
                $response->addAlert('修改字典项成功！');
            }
            else {
                $id = $this->dictItemModel->add($data);
                $response->addAlert('添加字典项成功！');
            }

            $data['id'] = $id;
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
            $this->dictModel->delete($id);
            $response->addResponse('reload-item');
            $response->addAlert('删除成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }

    /**
     * 删除字典项
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxDeleteItem(AjaxResponse $response) {
        $id = wd_input('id');
        if (! $id) {
            throw new ValidatedException("Error: 传入参数不正确");
        }
        
        try {
            $this->dictItemModel->delete($id);    
            
            $response->addResponse('reload-items');
            $response->addAlert('删除成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }
}