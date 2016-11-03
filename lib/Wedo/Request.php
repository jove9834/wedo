<?php
/**
 * 请求
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

/**
 * 请求
 */
class Request {
    /**
     * Method, GET/POST/PUT/DEL
     *
     * @var string
     */
    protected $_method = 'GET';

    /**
     * 模块名称
     *
     * @var string
     */
    protected $_module;

    /**
     * 控制器名称
     *
     * @var string
     */
    protected $_controller;

    /**
     * Action
     *
     * @var string
     */
    protected $_action;

    /**
     * 参数
     *
     * @var array
     */
    protected $_params;

    /**
     * 客户端语言
     *
     * @var string
     */
    protected $_language;

    /**
     * base uri
     *
     * @var string
     */
    protected $_base_uri;

    /**
     * request uri
     *
     * @var string
     */
    protected $_request_uri;

    /**
     * 是否已分发
     *
     * @var boolean
     */
    protected $_dispatched;

    /**
     * 是否已路由
     *
     * @var boolean
     */
    protected $_routed;

    /**
     * 客户端IP地址
     *
     * @var string
     */
    protected $_ip_address = FALSE;

    /**
     * 每个请求生成唯一的序列ID
     *
     * @var string
     */
    protected $_uuid;

    /**
     * 客户端信息
     *
     * @var Browser
     */
    protected $_browser;

    /**
     * 构造函数
     */
    public function __construct() {
        // 为每个请求生成唯一序列
        $this->_uuid = uuid();
        $this->_request_uri = $this->prepareRequestUri();
        $this->_base_uri = $this->prepareBaseUri();
        Logger::debug('uri:{}, _base_uri:{}', $this->_request_uri, $this->_base_uri);
    }

    /**
     * 筹备请求URI
     *
     * @return string
     */
    public function prepareRequestUri() {
        // 判断是否是CLI
        if (Application::app()->isCli()) {
            return $this->parseCliArgs();
        }

        if (! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            return '';
        }

        // 获取HTTP请求的方法，GET/POST/PUT/DEL
        $this->_method = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url($_SERVER['REQUEST_URI']);
        $query = isset($uri['query']) ? $uri['query'] : '';
        $uri = isset($uri['path']) ? rawurldecode($uri['path']) : '';

        if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
            $uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }

        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0) {
            $query = explode('?', $query, 2);
            $uri = rawurldecode($query[0]);
            $_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
        } else {
            $_SERVER['QUERY_STRING'] = $query;
        }

        parse_str($_SERVER['QUERY_STRING'], $_GET);

        if ($uri === '/' || $uri === '') {
            return '/';
        }

        return $this->removeRelativeDirectory($uri);
    }

    /**
     * Parse cli arguments
     *
     * Take each command line argument and assume it is a URI segment.
     *
     * @return string
     */
    private function parseCliArgs() {
        $args = array_slice($_SERVER['argv'], 1);
        return $args ? implode('/', $args) : '';
    }

    /**
     * base uri
     *
     * @return string
     */
    public function prepareBaseUri() {
        if (! isset($_SERVER['SCRIPT_NAME'])) {
            return '';
        }

        return dirname($_SERVER['SCRIPT_NAME']);
    }

    /**
     * 设置模块名称
     *
     * @param string $module 模块名称
     * @return Request
     */
    public function setModule($module) {
        $this->_module = $module;
        return $this;
    }

    /**
     * 获取模块名称
     *
     * @return string
     */
    public function getModule() {
        return $this->_module;
    }

    /**
     * 设置控制器名称
     *
     * @param string $controller 控制器名称
     * @return Request
     */
    public function setController($controller) {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * 获取控制器名称
     *
     * @return string
     */
    public function getController() {
        return $this->_controller;
    }

    /**
     * 设置Action名称
     *
     * @param string $action Action名称，不含后缀Action
     * @return Request
     */
    public function setAction($action) {
        $this->_action = $action;
        return $this;
    }

    /**
     * 获取Action名称，不含后缀Action
     *
     * @return string
     */
    public function getAction() {
        return $this->_action;
    }

    /**
     * 设置参数
     *
     * @param array $params 参数数组
     * @return Request
     */
    public function setParams(array $params) {
        $this->_params = $params;
        return $this;
    }  

    /**
     * 获取参数
     *
     * @return array
     */
    public function getParams() {
        return is_array($this->_params) ? $this->_params : array();
    }

    /**
     * 获取请求的method
     *
     * @return string
     */
    public function getMethod() {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * 设置请求的URI
     *
     * @param string $uri 请求的URI
     * @return Request
     */
    public function setRequestUri($uri) {
        $this->_request_uri = $uri;
        return $this;
    }

    /**
     * 获取请求的URI
     *
     * @return string
     */
    public function getRequestUri() {
        return $this->_request_uri;
    }

    /**
     * 获取BaseUri
     *
     * @return string
     */
    public function getBaseUri() {
        return $this->_base_uri;
    }

    /**
     * Remove relative directory (../) and multi slashes (///)
     *
     * Do some final cleaning of the URI and return it, currently only used in self::_parse_request_uri()
     *
     * @param string $uri URI
     * @return string
     * @internal param string $url
     */
    protected function removeRelativeDirectory($uri) {
        $uris = array();
        $tok = strtok($uri, '/');
        while ($tok !== FALSE) {
            if ((! empty($tok) || $tok === '0') && $tok !== '..') {
                $uris[] = $tok;
            }

            $tok = strtok('/');
        }

        return implode('/', $uris);
    }

    /**
     * 获取Get参数
     *
     * @param string $name    参数名称
     * @param string $default 默认值
     * @return string|array
     */
    public function getQuery($name = NULL, $default = NULL) {
        if ($name) {
            return wd_array_val($_GET, $name, $default);
        }
        else {
            return $_GET;
        }
    }

    /**
     * 获取Post参数
     *
     * @param string $name    参数名称
     * @param string $default 默认值
     * @return string|array
     */
    public function getPost($name = NULL, $default = NULL) {
        if ($name) {
            return wd_array_val($_POST, $name, $default);
        }
        else {
            return $_POST;
        }
    }

    /**
     * Fetch the IP Address
     *
     * Determines and validates the visitor's IP address.
     *
     * @return  string  IP address
     */
    public function getIPAddress() {
        if ($this->_ip_address !== FALSE) {
            return $this->_ip_address;
        }

        $proxy_ips = Config::get('proxy_ips');
        if (! empty($proxy_ips) && ! is_array($proxy_ips)) {
            $proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
        }

        $this->_ip_address = $_SERVER['REMOTE_ADDR'];
        if ($proxy_ips) {
            $spoof = NULL;
            foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header) {
                if (($spoof = $_SERVER[$header]) !== NULL) {
                    // Some proxies typically list the whole chain of IP
                    // addresses through which the client has reached us.
                    // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                    sscanf($spoof, '%[^,]', $spoof);

                    if (! $this->valid_ip($spoof)) {
                        $spoof = NULL;
                    }
                    else {
                        break;
                    }
                }
            }

            if ($spoof) {
                for ($i = 0, $c = count($proxy_ips); $i < $c; $i++) {
                    // Check if we have an IP address or a subnet
                    if (strpos($proxy_ips[$i], '/') === FALSE) {
                        // An IP address (and not a subnet) is specified.
                        // We can compare right away.
                        if ($proxy_ips[$i] === $this->_ip_address) {
                            $this->_ip_address = $spoof;
                            break;
                        }

                        continue;
                    }

                    // We have a subnet ... now the heavy lifting begins
                    isset($separator) OR $separator = $this->valid_ip($this->_ip_address, 'ipv6') ? ':' : '.';

                    // If the proxy entry doesn't match the IP protocol - skip it
                    if (strpos($proxy_ips[$i], $separator) === FALSE) {
                        continue;
                    }

                    // Convert the REMOTE_ADDR IP address to binary, if needed
                    if (! isset($ip, $sprintf)) {
                        if ($separator === ':') {
                            // Make sure we're have the "full" IPv6 format
                            $ip = explode(':',
                                str_replace('::',
                                    str_repeat(':', 9 - substr_count($this->_ip_address, ':')),
                                    $this->_ip_address
                                )
                            );

                            for ($i = 0; $i < 8; $i++) {
                                $ip[$i] = intval($ip[$i], 16);
                            }

                            $sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
                        }
                        else {
                            $ip = explode('.', $this->_ip_address);
                            $sprintf = '%08b%08b%08b%08b';
                        }

                        $ip = vsprintf($sprintf, $ip);
                    }

                    // Split the netmask length off the network address
                    $netAddr = '';
                    $masklen = 0;
                    sscanf($proxy_ips[$i], '%[^/]/%d', $netAddr, $masklen);

                    // Again, an IPv6 address is most likely in a compressed form
                    if ($separator === ':')
                    {
                        $netAddr = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($netAddr, ':')), $netAddr));
                        for ($i = 0; $i < 8; $i++)
                        {
                            $netAddr[$i] = intval($netAddr[$i], 16);
                        }
                    }
                    else
                    {
                        $netAddr = explode('.', $netAddr);
                    }

                    // Convert to binary and finally compare
                    if (strncmp($ip, vsprintf($sprintf, $netAddr), $masklen) === 0)
                    {
                        $this->_ip_address = $spoof;
                        break;
                    }
                }
            }
        }

        if (! $this->valid_ip($this->_ip_address)) {
            return $this->_ip_address = '0.0.0.0';
        }

        return $this->_ip_address;
    }

    /**
     * Validate IP Address
     *
     * @param   string  $ip IP address
     * @param   string  $which  IP protocol: 'ipv4' or 'ipv6'
     * @return  bool
     */
    public function valid_ip($ip, $which = '') {
        switch (strtolower($which)) {
            case 'ipv4':
                $which = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $which = FILTER_FLAG_IPV6;
                break;
            default:
                $which = NULL;
                break;
        }

        return (bool) filter_var($ip, FILTER_VALIDATE_IP, $which);
    }

    /**
     * Is AJAX request?
     *
     * Test to see if a request contains the HTTP_X_REQUESTED_WITH header.
     *
     * @return  bool
     */
    public function isAjaxRequest() {
        return (! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * 获取请求的UUID
     *
     * @return string
     */
    public function getUUID() {
        return $this->_uuid;
    }

    /**
     * 获取客户端浏览器信息
     *
     * @return Browser
     */
    public function getBrowser() {
        if (! $this->_browser) {
            $this->_browser = new Browser();

        }

        return $this->_browser;
    }
}