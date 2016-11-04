<?php
namespace Common;

use Wedo\Dispatcher;
use Wedo\Logger;
use Wedo\View\PhpEngine;
use Apps\Sys\Utils\User;
use Apps\Sys\Utils\Menu;

class BaseController extends Controller {
    /**
     * 是否允许匿名访问
     *
     * @var boolean
     */
    protected $anonymous = FALSE;

    /**
     * Ajax全局命令
     *
     * @var string
     */
    protected $ajaxSTDCMDConfig = array();

    public function __construct() {        
        //判断是否安装
        // if(!file_exists(DATAPATH . 'install.lock')){
        //     sendRedirect(getUrl('wizard', array(), 'install'), 'refresh');
        // }

        define('CURR_TIME', time());
        Logger::debug('anonymous:{}', $this->anonymous);
        if (! $this->anonymous && ! User::isLogined()) {
            // 不允许匿名访问，未登录
            // 跳转到登录界面
            $this->redirect(wd_url('auth', 'login', NULL, 'index'));
        }

        // 初始化用户环境
        $this->initUserEnvironment();
        
        // 定义操作编号
        if (!defined('WD_OPERATE_NO')) {
            define('WD_OPERATE_NO', Dispatcher::instance()->getRequest()->getUUID());
        }
        
        parent::__construct();
    } // end __construct()

    /**
     * Display
     *
     * @param string $tpl      视图模板文件(可选)
     * @param array  $tpl_vars 视图变量(可选)
     * @return void
     */
    protected function display($tpl = NULL, array $tpl_vars = NULL) {
        $page_url = wd_controller_url(); 
        $ajax_url = wd_controller_url('ajax');
        if ($tpl_vars) {
            $tpl_vars = array_merge($tpl_vars, array('page_url' => $page_url, 'ajax_url' => $ajax_url));
        }
        else {
            $tpl_vars = array('page_url' => $page_url, 'ajax_url' => $ajax_url);   
        }

        echo $this->render($tpl, $tpl_vars);
    }

    /**
     * 初始化用户环境
     *
     * @return void
     */
    protected function initUserEnvironment() {
        // 当前请求功能名称
        $request = Dispatcher::instance()->getRequest();
        $m = $request->getModule();
        $c = $request->getController();

        $functionName = Menu::autoStoreFunction($m, $c);
        defined('CURRENT_FUNCTION') or define('CURRENT_FUNCTION', $functionName); 
        
        if (! $this->anonymous && User::isLogined()) {
            // 取用户属性
            $profile = User::getUserProfile();
            if ($profile) {
                // 取语言
                $lang = wd_array_val($profile, 'language');
                // 取时区
                $timezone = wd_array_val($profile, 'timezone');
                // 取主题
                $template = wd_array_val($profile, 'template');
                // 取皮肤
                $theme = wd_array_val($profile, 'theme');
            }
        }

        $lang = isset($lang) && $lang ?: getClientLang(); // 默认为客户端语言,只支持简体中文、繁体中文、英文
        $timezone = isset($timezone) && $timezone ?: 'Etc/GMT-8'; // 默认为北京时间
        $template = isset($template) && $template ?: 'smart'; // 默认 igg inspinia clear
        $theme = isset($theme) && $theme ?: 'default'; // 默认

        defined('LANGUAGE') or define('LANGUAGE', $lang); 
        date_default_timezone_set($timezone);
        defined('TEMPLATE') or define('TEMPLATE', $template); 
        defined('THEME') or define('THEME', $theme); 
    }
    
    /**
     * 输出表格数据
     *
     * @param AjaxResponse $response AjaxResponse
     * @param string       $tableId  表格ID
     * @param array        $ds       数据源
     * @param integer      $total    总记录数
     * @param integer      $page     当前页码
     * @param integer      $pageSize 每页显示记录数
     * @param array        $vars     参数
     * @return void
     */
//    public function gridRender($response, $tableId, array $ds, $total = 0, $page = 1, $pageSize = 15, array $vars = array()) {
//        $vars = array_merge($vars, array('ds_' . $tableId => $ds));
//
//        // 取table模板
//        $template_file = \Wedo\Ui\Grid\Grid::getCacheFile($tableId);
//        $engine = new PhpEngine();
//        $content = $engine->evaluatePath($template_file, $vars);
//        $page = $page ? (int)$page : 1;
//        $response->addData(array('data' => $content, 'total' => $total, 'page' => $page));
//    }

    /**
     * 输出表格数据
     *
     * @param AjaxResponse $response  AjaxResponse
     * @param string       $dbgridKey DBGrid配置项
     * @param array        $ds        数据源
     * @param integer      $total     总记录数
     * @param integer      $page      当前页码
     * @param integer      $pageSize  每页显示记录数
     * @param array        $vars      参数
     * @return void
     */
    public function jqgridRender($response, $dbgridKey, array $ds, $total = 0, $page = 1, $pageSize = 15, array $vars = array()) {
        $page = $page ? (int)$page : 1;
        $pages = ceil($total / $pageSize);
        // $grid = GridApi::getGridConfig($dbgridKey);
        // $result = array();
        // foreach ($ds as $row) {
        //     $config = $grid
        // }
        $data = array('records' => $total, 'page' => $page, 'total' => $pages, 'rows' => $ds);
        // if ($sidx) {
        //     $data['sidx'] = $sidx;
        //     $data['sord'] = $sord;
        // }

        $response->addData($data)->sendJQGrid();
    }

    /**
     * 输出树型数据
     *
     * @param AjaxResponse $response AjaxResponse
     * @param string       $treeId   树ID
     * @param array        $ds       数据源
     * @return void
     */
    public function treeRender($response, $treeId, array $ds) {
        // AjaxTree 使用以下代码
        $data = array('tree_id' => $treeId, 'data' => $ds);
        $response->addData($data);        
    }

    /**
     * 输出树型数据
     *
     * @param AjaxResponse $response AjaxResponse
     * @param array        $ds       数据源
     * @return void
     */
    public function jstreeRender($response, array $ds) {
        // JsTree
        $response->sendJsonData($ds);
    }

    /**
     * 获取当前功能URL
     *
     * @return string
     */
    protected function getCurrentFunctionUrl() {
        $request = Dispatcher::instance()->getRequest();
        $m = $request->getModule();
        $c = $request->getController();
        
        $router = Dispatcher::instance()->getRouter();
        $info = array(':m' => $m, ':c' => $c);
        
        return $router->getCurrentRoute()->assemble($info);
    }
}
