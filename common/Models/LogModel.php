<?php
/**
 * 操作日志模型文件
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Common\Models;

use Common\BaseModel;
use Wedo\Dispatcher;

/**
 * 操作日志
 */
class LogModel extends BaseModel {
	/**
     * 表名
     *
     * @var string
     */
    protected $table = 'sys_log';
    /**
     * 值唯一的字段
     *
     * @var string|array
     */
    protected $uniqueColumn = NULL;

	/**
	 * 添加操作日志
	 *
	 * @param string $log_type     日志类型
	 * @param string $operate_type 操作类型
     * @param string $description  操作描述
	 * @param string $module       模块名称
	 * @param string $operator     操作人ID
     * @return int
	 **/
	public function writeLog($log_type, $operate_type, $description, $module = FALSE, $operator = FALSE) {
		$ip_address = ip_address_pton();
        $request = Dispatcher::getInstance()->getRequest();
        $serialVersionUID = defined('WD_OPERATE_NO') ? WD_OPERATE_NO : NULL;
        $operator = $operator ?: (defined('LOGIN_UID') ? LOGIN_UID : 0);
        $browserType = '';
        $device = '';
        $os = '';

        if ($request) {            
            $module = $module ?: $request->getModule();
            $browser = $request->getBrowser();        
            $browserType = $browser->getName() . ' ' . $browser->getVersion();
            $device = $browser->getDevice();
            $os = $browser->getPlatform();
        }

		$data = array(
            'req_serial_id' => $serialVersionUID,
			'log_type' => $log_type,
			'operate_type' => $operate_type,
			'description' => $description,
			'create_at' => time(),
            'ip_address' => $ip_address,
            'device' => $device,
            'os' => $os,
            'browser_type' => $browserType,
			'uid' => $operator,
		);		

		return $this->add($data);
	}
}