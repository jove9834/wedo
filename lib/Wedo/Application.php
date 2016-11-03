<?php
/**
 * Application
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

/**
 * Application
 */
class Application
{
    /**
     * 应用版本号
     *
     * @var string
     */
    const APP_VERSION = '1.0';

    /**
     * Application 实例
     *
     * @var Application
     */
    protected static $_app ;

    /**
     * 模块数组
     *
     * @var array
     */
    protected $_modules = array();

    /**
     * 环境名称
     *
     * @var string
     */
    protected $_environ = 'production';

    /**
     * App应用目录
     *
     * @var string
     */
    protected $_app_directory;

    /**
     * App应用命名空间
     *
     * @var string
     */
    protected $_apps_ns_prefix = 'Apps\\';

    /**
     * 语言类实例
     *
     * @var \Wedo\Language
     */
    protected $_language;

    /**
     * 字符集
     * 
     * @var string the charset currently used for the application. Defaults to 'UTF-8'.
     */
    public $charset = 'UTF-8';

    /**
     * Application constructor.
     *
     * @param string $envrion
     */
    public function __construct($envrion = NULL)
    {
        self::$_app = &$this;
        $envrion OR $this->_environ = $envrion;
        
        // 定义路径常量
        $this->definePathConst();
        // 自动加载类
        $this->autoLoadClass();
    }

    /**
     * 获取Application实例
     *
     * @return \Wedo\Application
     */
    public static function app()
    {
        return self::$_app;
    }

    /**
     * 初始化应用
     *
     * @return void
     */
    private function init()
    {
        defined('WEDO_TRACE_LEVEL') or define('WEDO_TRACE_LEVEL', 0);
        defined('WEDO_DEBUG') or define('WEDO_DEBUG', TRUE);
        // 初始化系统处理
        $this->initSystemHandlers();

        // 初始化语言
        $this->_language = new Language();

        // 加载配置
        Config::load('config');
        // 应用命名空间前缀默认为Apps\\，存放目录为apps下
        $this->_apps_ns_prefix = Config::get('apps_ns_prefix', 'Apps\\');
        // 注册类加载器
        $classLoader = new ClassLoader();
        if ($this->_apps_ns_prefix) {
            $classLoader->addPsr4($this->_apps_ns_prefix, array($this->_app_directory));
        }
        
        $classLoader->register();
    }

    /**
     * Run application
     *
     * @return void
     */
    public function run()
    {
        // 初始化
        $this->init();
        // 解析请求
        Dispatcher::getInstance()->dispatch(new Request());
    }

    /**
     * 运行测试实例
     *
     * @throws \Exception
     */
    public function testing()
    {
        $this->init();
        $args = array_slice($_SERVER['argv'], 1);
        foreach ($args as $val) {
            if (! $val) {
                continue;
            }

            Logger::debug('arg : {}', $val);
            try {
                $test = new $val();    
                if (! $test instanceof TestCase) {
                    throw new \Exception('测试用例必须继承TestCase类');
                }

                $test->run($val);
            } catch (\Wedo\Exception\ClassNotFoundException $e) {
                Logger::error('测试类不存在：' . $e->getMessage());
            }
        }
    }

    /**
     * 环境名称
     *
     * @return string
     */
    public function environ()
    {
        return $this->_environ;
    }

    /**
     * 取APP命名空间前缀
     *
     * @return string
     */
    public function getAppsNsPrefix()
    {
        return $this->_apps_ns_prefix;
    }

    /**
     * 取模块数组
     *
     * @return array
     * @throws \Exception
     */
    public function getModules()
    {
        if ($this->_modules) {
            return $this->_modules;
        }
                
        $fs = new \Wedo\Filesystem();
        $directories = $fs->directories($this->_app_directory);
        if (! $directories) {
            throw new \Exception(wd_print("{}目录下没有默认的模块{}", $this->_app_directory, Dispatcher::getInstance()->getDefaultModule()));
        }

        foreach ($directories as $dir) {
            $this->_modules[] = ucfirst(basename($dir));
        }

        return $this->_modules;
    }

    /**
     * 自动加载类
     *
     * @return void
     */
    protected function autoLoadClass()
    {
        $autoClass = $this->getAutoLoadClass();
        foreach ($autoClass as $key => $file) {
            require_once $file;
        }
    }

    /**
     * 获取需要自动加载的类配置
     *
     * @return array
     */
    private function getAutoLoadClass()
    {
        return array(
            'Common' => __DIR__ . '/Common.php',
            'ClassLoader' => __DIR__ . '/ClassLoader.php',
            'Language' => __DIR__ . '/Language.php',
            'helpers' => __DIR__ . '/Support/helpers.php',
            'Config' => __DIR__ . '/Config.php',
            'Logger' => __DIR__ . '/Logger.php',
        );
    }

    /**
     * 定义路径常量
     *
     * @return void
     * @throws \Exception
     */
    protected function definePathConst()
    {
        if (! defined('BASE_PATH')) {
            throw new \Exception('必须先设置BASE_PATH常量');
        }

        if (! defined('APP_PATH')) {
            define('APP_PATH', BASE_PATH . '/apps');
        }

        if (! defined('CORE_PATH')) {
            define('CORE_PATH', dirname(__FILE__));
            define('DATA_PATH', BASE_PATH . '/data');
            define('PUBLIC_PATH', BASE_PATH . '/public');
            define('LIB_PATH', BASE_PATH . '/lib');
        }

        if (! $this->_app_directory) {
            $this->_app_directory = BASE_PATH . '/apps';
        }
    }

    /**
     * 判断是否CLI
     *
     * @return boolean
     */
    public function isCli()
    {
        return php_sapi_name() == 'cli';
    }

    /**
     * 取应用路径
     *
     * @return string
     */
    public function getAppDirectory()
    {
        return $this->_app_directory;
    }

    /**
     * 取分发类实例
     *
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return Dispatcher::getInstance();
    }

    /**
     * 获取语言类实例
     *
     * @return Language
     */
    public function language()
    {
        $this->_language OR $this->_language = new Language();
        return $this->_language;
    }

    /**
     * 取框架版本号
     *
     * @return string
     */
    public function getVersion()
    {
        return self::APP_VERSION;
    }

    /**
     * Initializes the class autoloader and error handlers.
     *
     * @return void
     */
    protected function initSystemHandlers()
    {
        set_exception_handler(array($this,'handleException'));
        set_error_handler(array($this,'handleError'),error_reporting());
    }

    /**
     * Displays the uncaught PHP exception.
     *
     * This method displays the exception in HTML when there is
     * no active error handler.
     *
     * @param \Exception $exception the uncaught exception
     */
    public function displayException(\Exception $exception)
    {
        if(WEDO_DEBUG) {
            echo '<h1>' . get_class($exception) . "</h1>\n";
            echo '<p>' . $exception->getMessage() . ' (' . $exception->getFile() . ':' . $exception->getLine() . ')</p>';
            echo '<pre>' . $exception->getTraceAsString() . '</pre>';
        } else {
            echo '<h1>' . get_class($exception) . "</h1>\n";
            echo '<p>' . $exception->getMessage() . '</p>';
        }
    }

    /**
     * Displays the captured PHP error.
     *
     * This method displays the error in HTML when there is
     * no active error handler.
     *
     * @param integer $code error code
     * @param string $message error message
     * @param string $file error file
     * @param string $line error line
     */
    public function displayError($code,$message,$file,$line)
    {
        if (WEDO_DEBUG) {
            echo "<h1>PHP Error [$code]</h1>\n";
            echo "<p>$message ($file:$line)</p>\n";
            echo '<pre>';

            $trace = debug_backtrace();
            // skip the first 3 stacks as they do not tell the error position
            if (count($trace) > 3) {
                $trace = array_slice($trace, 3);
            }

            foreach ($trace as $i => $t) {
                if (!isset($t['file'])) {
                    $t['file'] = 'unknown';
                }

                if (!isset($t['line'])) {
                    $t['line'] = 0;
                }

                if (!isset($t['function'])) {
                    $t['function'] = 'unknown';
                }

                echo "#$i {$t['file']}({$t['line']}): ";
                if (isset($t['object']) && is_object($t['object'])) {
                    echo get_class($t['object']).'->';
                }

                echo "{$t['function']}()\n";
            }

            echo '</pre>';
        } else {
            echo "<h1>PHP Error [$code]</h1>\n";
            echo "<p>$message</p>\n";
        }
    }

    /**
     * Handles uncaught PHP exceptions.
     *
     * This method is implemented as a PHP exception handler. It requires
     * that constant YII_ENABLE_EXCEPTION_HANDLER be defined true.
     *
     * This method will first raise an {@link onException} event.
     * If the exception is not handled by any event handler, it will call
     * {@link getErrorHandler errorHandler} to process the exception.
     *
     * The application will be terminated by this method.
     *
     * @param \Exception $exception exception that is not caught
     */
    public function handleException(\Exception $exception)
    {
        // disable error capturing to avoid recursive errors
        restore_error_handler();
        restore_exception_handler();

        // php <5.2 doesn't support string conversion auto-magically
        $message = $exception->__toString();
        if(isset($_SERVER['REQUEST_URI'])) {
            $message .= "\nREQUEST_URI=" . $_SERVER['REQUEST_URI'];
        }

        if(isset($_SERVER['HTTP_REFERER'])) {
            $message .= "\nHTTP_REFERER=" . $_SERVER['HTTP_REFERER'];
        }

        Logger::error($message);

        if ($this->isCli()) {
            echo $message;
            exit(1);
        }

        try {
            $event = new \Wedo\Exception\CExceptionEvent($this, $exception);
            if (! $event->handled) {
                // try an error handler
                $handler = new \Wedo\Exception\CErrorHandler();
                $handler->handle($event);
            }
        } catch(\Exception $e) {
            $this->displayException($e);
        }

        exit(1);
    }

    /**
     * Handles PHP execution errors such as warnings, notices.
     *
     * This method is implemented as a PHP error handler. It requires
     * that constant YII_ENABLE_ERROR_HANDLER be defined true.
     *
     * This method will first raise an {@link onError} event.
     * If the error is not handled by any event handler, it will call
     * {@link getErrorHandler errorHandler} to process the error.
     *
     * The application will be terminated by this method.
     *
     * @param integer $code the level of the error raised
     * @param string $message the error message
     * @param string $file the filename that the error was raised in
     * @param integer $line the line number the error was raised at
     */
    public function handleError($code,$message,$file,$line)
    {
        if($code & error_reporting()) {
            // disable error capturing to avoid recursive errors
            restore_error_handler();
            restore_exception_handler();

            $log = "$message ($file:$line)\nStack trace:\n";
            $trace = debug_backtrace();
            // skip the first 3 stacks as they do not tell the error position
            if(count($trace)>3) {
                $trace = array_slice($trace,3);
            }

            foreach($trace as $i=>$t) {
                if(!isset($t['file']))
                    $t['file'] = 'unknown';
                if(!isset($t['line']))
                    $t['line'] = 0;
                if(!isset($t['function']))
                    $t['function'] = 'unknown';
                $log .= "#$i {$t['file']}({$t['line']}): ";
                if(isset($t['object']) && is_object($t['object']))
                    $log .= get_class($t['object']).'->';
                $log .= "{$t['function']}()\n";
            }

            if (isset($_SERVER['REQUEST_URI'])) {
                $log .= 'REQUEST_URI='.$_SERVER['REQUEST_URI'];
            }

            Logger::error($log . "\n");
            if ($this->isCli()) {
                echo $log;
                exit(1);
            }

            try {
                $event = new \Wedo\Exception\CErrorEvent($this, $code, $message, $file, $line);
                if(! $event->handled) {
                    // try an error handler
                    $handler = new \Wedo\Exception\CErrorHandler();
                    $handler->handle($event);
                }
            } catch(\Exception $e) {
                $this->displayException($e);
            }

            exit(1);
        }
    }

}