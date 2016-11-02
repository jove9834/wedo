<?php
/**
 * 日志类
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

/**
 * 日志类
 */
class Logger {

    /**
     * Format of timestamp for log files
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 日志级别
     *
     * @var integer
     */
    protected $level = 2;

    /**
     * 日志级别枚举
     *
     * @var array
     */
    private static $ENUM_LEVEL = array('OFF' => 0, 'DEBUG' => 1, 'INFO' => 2, 'WARN' => 3, 'ERROR' => 4);

    /**
     * 日志路径
     *
     * @var integer
     */
    protected $path = NULL;

    /**
     * 单例模式
     *
     * @var Log
     */
    private static $_instance;

    /**
     * 构造函数
     */
    private function __construct() {
        // 加载日志配置
        $level_str = Config::get('log.level');
        $this->path = Config::get('log.path');

        if ($level_str) {
            $level_str = strtoupper($level_str);
            isset(self::$ENUM_LEVEL[$level_str]) && $this->level = self::$ENUM_LEVEL[$level_str];
        }

        $this->path || $this->path = DATA_PATH . '/logs/';
        wd_mkdirs($this->path);
    }

    /**
     * 单例模式
     *
     * @return Logger
     */
    public static function getInstance() {
        if (! self::$_instance) {
            self::$_instance = new Logger();
        }

        return self::$_instance;
    }

    /**
     * 写调试日志
     *
     * 支持带参数的消息，如：
     * Logger::debug('username:{}, age: {}', $username, $age);
     *
     * @param string|array $msg
     * @return boolean
     */
    public static function debug($msg) {
        $params  = func_get_args();
        if (count($params) > 1) {
            array_shift($params);
        }
        else {
            $params = array();
        }

        return Logger::getInstance()->write('debug', $msg, $params);
    }

    /**
     * 写错误日志
     *
     * @param string|array $msg
     * @return boolean
     */
    public static function error($msg) {
        $params  = func_get_args();
        if (count($params) > 1) {
            array_shift($params);
        }
        else {
            $params = array();
        }

        return Logger::getInstance()->write('error', $msg, $params);
    }

    /**
     * 写信息日志
     *
     * @param string|array $msg
     * @return boolean
     */
    public static function info($msg) {
        $params  = func_get_args();
        if (count($params) > 1) {
            array_shift($params);
        }
        else {
            $params = array();
        }

        return Logger::getInstance()->write('info', $msg, $params);
    }
    
    /**
     * 写日志文件
     *
     * Generally this function will be called using the global log_message() function
     *
     * @param string $level  the error level: 'error', 'debug' or 'info'
     * @param string $msg    the error message
     * @param array  $params 参数
     * @return  bool
     */
    private function write($level, $msg, array $params = array()) {
        $level = strtoupper($level);
        $iLevel = isset(self::$ENUM_LEVEL[$level]) ? self::$ENUM_LEVEL[$level] : 1;
        if ($this->level === 0 || $iLevel < $this->level) {
            return ;
        }

        $path = rtrim($this->path, '/') . '/';
        
        $filepath = $path . 'wedo-'.date('Y-m-d').'.log';

        if ( ! file_exists($filepath)){
            $newfile = TRUE;
        }

        if (! $fp = fopen($filepath, 'ab')) {
            return FALSE;
        }

        if(is_array($msg)) {
            // $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            $msg = print_r($msg, TRUE);
        }
        else {
            $msg = parse_string($msg, $params);
        }

        $message = $this->getMessage($level, $msg);
        
        flock($fp, LOCK_EX);

        for ($written = 0, $length = strlen($message); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr($message, $written))) === FALSE) {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        if (isset($newfile) && $newfile === TRUE) {
            @chmod($filepath, 0666);
        }

        return is_int($result);
    }

    private function getMessage($level, $msg) {
        $traces = debug_backtrace();
        if ($level == 'ERROR') {
            // 输出错误信息及跟踪
            $message = '[' . date($this->dateFormat) . '] ' . $level . ' --> ' . $msg . "\n";
            return $message;
        }


        $pos = '';
        foreach ($traces as $trace) {
            if (isset($trace['file'],$trace['line']) && strpos($trace['file'], __FILE__) !== 0) {
                $pos = $trace['file'] . ' ('.$trace['line'].')';
                break;
            }
        }

        $message = '[' . date($this->dateFormat) . '] ' . $level . ' - ' . $pos . ' --> ' . $msg . "\n";
        return $message;
    }
}