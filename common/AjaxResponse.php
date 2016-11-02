<?php
/**
 * 处理Ajax请求响应
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common;

/**
 * 处理响应
 *
 */
class AjaxResponse {
    /**
     * 响应数据
     *
     * @var string
     **/
    private $responseData = array();

    /**
     * 默认的命令
     *
     * @var string
     **/
    private $defaultCmd = NULL;

    /**
     * 构造函数
     */
    public function __construct($cmd = NULL) {
        $this->defaultCmd = $cmd;
    }

    /**
     * 设置默认的命令
     * 
     * @param string $cmd 命令
     * @return AjaxResponse 
     */
    public function setDefaultCmd($cmd) {
        $this->defaultCmd = $cmd;
        return $this;
    }


    /**
     * 发送提示信息
     * 
     * @param string $msg 提示消息
     * @return AjaxResponse 
     */
    public function addAlert($msg) {
        if (is_array($msg)) {
            $ret = '<ul>';
            foreach ($msg as $key => $value) {
                $ret .= "<li>{$value}</li>";
            }

            $ret .= '</ul>';
            $msg = $ret;
        }
        
        $this->addResponse('alert', $msg);
        return $this;
    }

    /**
     * 发送提示错误消息
     * 
     * @param string $msg 错误消息
     * @return AjaxResponse 
     */
    public function addError($msg) {
        if (is_array($msg)) {
            $ret = '<ul>';
            foreach ($msg as $key => $value) {
                $ret .= "<li>{$value}</li>";
            }

            $ret .= '</ul>';
            $msg = $ret;
        }

        $this->addResponse('error', $msg);
        return $this;
    }

    /**
     * 发送执行客户端脚本
     * 
     * @param string $script 客户端脚本
     * @return AjaxResponse 
     */
    public function addScript($script) {
        $this->addResponse('script', $script);
        return $this;
    }

    /**
     * 发送强制退出命令
     * 
     * @return AjaxResponse 
     */
    public function addLogout() {
        $this->addResponse('logout');
        return $this;
    }

    /**
     * 发送登录超时命令
     * 
     * @return AjaxResponse 
     */
    public function addTimeout() {
        $this->addResponse('timeout');
        return $this;
    }

    /**
     * 发送刷新页面命令
     * 
     * @return AjaxResponse 
     */
    public function addReload() {
        $this->addResponse('reload');
        return $this;
    }

    public function addRedirect($url) {
        $this->addResponse('redirect', $url);
        return $this;
    }

    /**
     * 发送刷新表格数据命令
     * 
     * @param string $tableId 表格ID,用于一个界面多个表格情况，默认一个表格，不需要填写
     * @return AjaxResponse 
     */
    public function addRefreshTable($tableId = '') {
        $this->addResponse('refreshTable', $tableId);
        return $this;
    }

    /**
     * 重置表单
     * 
     * @param string $formId 表单ID
     * @return AjaxResponse 
     */
    public function addResetForm($formId = '') {
        $this->addResponse('resetForm', $formId);
        return $this;
    }

    /**
     * 发送返回数据
     * 
     * @param string $data 返回数据,字符串或数组
     * @return AjaxResponse 
     */
    public function addData($data = '') {
        $this->addResponse($this->defaultCmd, $data);
        return $this;
    }

    /**
     * 发送关闭窗口
     * 
     * @return AjaxResponse 
     */
    public function addCloseDialog() {
        $this->addResponse('closeDialog');
        return $this;
    }

    /**
     * 添加响应数据
     *
     * @param string $cmd  命令
     * @param string $data 命令参数，可以是数组或字符串
     * @return void
     */
    public function addResponse($cmd, $data = '') {
        $this->responseData[] = array('cmd' => $cmd, 'data' => $data);
        return $this;
    }

    public function getResponseData($cmd = NULL) {
        if ($cmd === NULL) {
            return $this->responseData;
        }

        foreach ($this->responseData as $item) {
            if (wd_array_val($item, 'cmd') == $cmd) {
                return wd_array_val($item, 'data');
            }
        }
        
        return NULL;
    }

    public function clear() {
        $this->responseData = array();
    }

    /**
     * 发送响应数据至客户端
     *
     * @return void
     */
    public function send() {
        if (! $this->responseData) {
            return;
        }

        // ob_clean();
        $this->responseData = array_reverse($this->responseData);
        $result = json_encode($this->responseData);
        echo $result;
    }

    /**
     * 发送响应数据至客户端
     *
     * @return void
     */
    public function sendJQGrid() {
        $data = $this->getResponseData($this->defaultCmd);        
        $result = json_encode($data);
        echo $result;
        $this->clear();
    }

    /**
     * 发送响应数据至客户端
     *
     * @return void
     */
    public function sendJsonData(array $data) {
        $result = json_encode($data);
        echo $result;        
    }
    
}