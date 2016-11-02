<?php
/**
 * DB帐号管理控制器
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
use Common\Models\DBModel;


/**
 * DB帐号管理
 */
class DBController extends BaseController {
    /**
     * DBModel
     *
     * @var DBModel
     */
    private $dbModel;

    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->dbModel = new DBModel();
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
            $query = array(array('c_lk_memo' => $keyword), array('c_lk_dbname' => $keyword), array('c_lk_username' => $keyword));
        }

        list($data, $total) = $this->dbModel->pagination($query, 'id desc', '*', $page, $pagesize);
       
        $response->addData(array('page' => $page, 'total' => $total, 'data' => $data));
    }
    
    /**
     * 保存信息
     *
     * @param AjaxResponse $response 响应对象
     * @return void
     */
    public function ajaxSave(AjaxResponse $response) {
        $data = wd_input('data');
        if (!wd_array_val($data, 'username') || !wd_array_val($data, 'dbname')) {
            throw new ValidatedException("Error: 传入参数不正确");
        }

        try {
            if ($id) {
                $this->dbModel->update($id, $data);
                $response->addAlert('修改成功！');
            }
            else {
                if (! wd_array_val($data, 'host')) {
                    $data['host'] = 'localhost';
                }
                
                if (! wd_array_val($data, 'password')) {
                    $data['password'] = '1';
                }

                $data['stauts'] = 1;

                $id = $this->dbModel->add($data);
                $response->addAlert('添加成功！');
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
            $this->dbModel->update($id, array('status' => 0));
            $response->addResponse('reload-item');
            $response->addAlert('删除成功！');
        } catch (\Exception $e) {
            $response->addError('错误：' . $e->getMessage());
        }
    }
}