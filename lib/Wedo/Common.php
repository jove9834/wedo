<?php
/**
 * 获取缓存目录
 * @param string $path
 * @return string
 */
function wd_cache_path($path) {
    $cache_path = Config::get('cache_path');
    $cache_path = (empty($cache_path)) ? DATAPATH : rtrim($cache_path,'/').'/';
    $path = $cache_path . $path;
    return rtrim($path, '/') . '/';
}

/**
 * 取数组值
 *
 * @param array   $array   数组
 * @param string  $key     键
 * @param boolean $default 默认值
 * @return mixed
 */
function wd_array_val(array $array, $key, $default = FALSE) {
    if (!$array) {
        return $default;
    }

    return isset($array[$key]) ? $array[$key] : $default;
}

function wd_object_vars($obj) {
    $vars = array();
    $reflect = new \ReflectionClass($obj);
    $props = $reflect->getProperties();
    if (! $props) {
        return $vars;
    }

    foreach ($props as $prop) {
        $vars[] = $prop->name;
    }

    return $vars;
}

function wd_explode($str, $delimiter = ',') {
    if (! $str) {
        return NULL;
    }

    if (is_array($str)) {
        return $str;
    }

    return explode($delimiter, $str);
}

function wd_options_text($options, $value, $preOptions = NULL) {
    if (! $options || $value == NULL) {
        return NULL;
    }

    if ($preOptions) {        
        foreach ($preOptions as $key => $value) {
            $options[$key] = $value;
        }
    }

    $ret = '';
    if (is_array($value)) {
        foreach ($value as $v) {
            $ret .= wd_array_val($options, $v) . ',';    
        }

        $ret = rtrim($ret, ',');
    }
    else {
        $ret = wd_array_val($options, $value);
    }

    return $ret;
}

/**
 * 字符串方式实现 preg_match("/(s1|s2|s3)/", $string, $match)
 *
 * @param string  $string      源字符串
 * @param array   $arr         要查找的字符串 如array('s1', 's2', 's3')
 * @param boolean $returnValue 是否返回找到的值
 * @return boolean
 */
function wd_istrpos( $string, $arr, $returnValue = false ) {
    if ( empty( $string ) ) {
        return false;
    }
    foreach ( (array) $arr as $v ) {
        if ( strpos( $string, $v ) !== false ) {
            $return = $returnValue ? $v : true;
            return $return;
        }
    }
    return false;
}

function wd_in_stringlist($search, $stringlist) {
    if (! $search || ! $stringlist) {
        return FALSE;
    }

    $array = wd_explode($stringlist);
    return in_array($search, $array);
}

function wd_studly($value) {
    $value = ucwords(str_replace(array('-', '_'), ' ', $value));
    return str_replace(' ', '', $value);
}

function wd_camel_case($str) {
    $i = array("-","_");
    $str = preg_replace('/([a-z])([A-Z])/', "\\1 \\2", $str);
    $str = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $str);
    $str = str_replace($i, ' ', $str);
    $str = str_replace(' ', '', ucwords(strtolower($str)));
    $str = strtolower(substr($str,0,1)).substr($str,1);
    return $str;
}

function wd_uncamel_case($str) {
    $str = preg_replace('/([a-z])([A-Z])/', "\\1_\\2", $str);
    $str = strtolower($str);
    return $str;
}

/**
 * 判断字符串是否是JSON格式
 *
 * @param string $string 字符串
 * @return boolean
 */
function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

/**
 * 判断字符串是否是JSON格式,只支持一维数组的JSON格式
 *
 * @param string $str 字符串
 * @return boolean
 */
function wd_is_json($str) {
    return preg_match('/^\{\s*( ("[^"]*"|\'[^\']*\'| [^\'",]+ )\s*:\s*(?2)[,]?\s* )+\s*\}$/xs', $str);
}

/**
 * 将数组转换为字符串
 *
 * @param array $arr 数组
 * @return string
 */
function wd_json_encode($arr) {
    if (is_array($arr)) {
        return json_encode($arr, JSON_UNESCAPED_UNICODE);    
    }
    
    return $arr;
}

/**
 * 将JSON字符串转换为数组
 *
 * @param string $str 字符串
 * @return array
 */
function wd_json_decode($str) {
    if (preg_match('/^\{\s*( ("[^"]*"|\'[^\']*\'| [^\'",]+ )\s*:\s*(?2)[,]?\s* )+\s*\}$/xs', $str)) {
        // Json数组
        preg_match_all('/(?: ( \s*"[^"]*"|\'[^\']*\'| [^\'",]+ )\s*:\s*((?1)))+/xs', $str, $match);
        $ret = [];
        foreach ($match[0] as $key => $value) {
            $name = trim($match[1][$key]);
            $val = trim($match[2][$key]);

            if (starts_with($name, '"') || starts_with($name, "'")) {
                $name = substr($name, 1, -1);
            }

            if (starts_with($val, '"') || starts_with($val, "'")) {
                $val = substr($val, 1, -1);
            }

            $ret[$name] = $val;
        }

        return $ret;
    }

    return NULL;
}

/**
 * 取输入参数
 * 
 * @param string $key
 * @param string $default
 * @return mixed
 */
function wd_input($key = NULL, $default = FALSE) {
    $request = Wedo\Dispatcher::instance()->getRequest();
    if ($key) {
        $result = $request->getQuery($key) ?: $request->getPost($key);
    }
    else {
        $result = array_merge($request->getQuery(), $request->getPost());
    }
    
    if ($result == NULL) {
        $result = $default;
    }
    
    return $result;
}

/**
 * 取翻译信息
 * 
 * @param string $text 翻译文本或KEY
 * @return string
 */
function lang($text) {
    return \Wedo\Application::app()->language()->get($text);
}

if ( ! function_exists('directoryMap')) {

    function directoryMap($source_dir, $directory_depth = 0, $hidden = FALSE) {
        if ($fp = @opendir($source_dir)) {
            $filedata   = array();
            $new_depth  = $directory_depth - 1;
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if ($file === '.' OR $file === '..' OR ($hidden === FALSE && $file[0] === '.'))
                {
                    continue;
                }

                is_dir($source_dir.$file) && $file .= DIRECTORY_SEPARATOR;

                if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir.$file))
                {
                    $filedata[$file] = directoryMap($source_dir.$file, $new_depth, $hidden);
                }
                else
                {
                    $filedata[] = $file;
                }
            }

            closedir($fp);
            return $filedata;
        }

        return FALSE;
    }
}

if (! function_exists('wd_load_class')) {
    /**
     * 第三方类库注册
     *
     * 加载并实例化无命名空间的类库
     *
     * @param string $class     类名，类名必须和文件名一致
     * @param string $param     构造函数参数
     * @param string $directory 所在目录，默认为ThirdParty
     * @return object
     */
    function wd_load_class($class, $param = NULL, $directory = 'third_party')
    {
        static $_classes = array();
    
        // Does the class exist? If so, we're done...
        if (isset($_classes[$class]))
        {
            return $_classes[$class];
        }
    
        $path = APPSPATH . '/common/';
    
        if (file_exists($path . $directory . '/' . $class . '.php'))
        {
            require_once $path . $directory . '/' . $class . '.php';
        }
    
        $_class = new ReflectionClass($class);
        $_classes[$class] = isset($param) ? $_class->newInstanceArgs($param) : $_class->newInstance();
        return $_classes[$class];
    }
}

if (! function_exists('wd_load_helper')) {
    /**
     * 加载全局辅助函数
     *
     * @param string $helper    文件名，不包含扩展名.php
     * @param string $directory 所在目录, 默认为Helpers，注意，所在的全局辅助函数文件必须存放于Common目录下
     * @return bool TRUE 加载成功，FALSE 加载失败
     */
    function wd_load_helper($helper, $basePath = NULL)
    {
        static $_helpers = array();
        
        if (!$basePath) {
            $basePath = COREPATH;
        }

        $file = parseWedoName($helper, 'helper', $basePath);
        
        // Does the class exist? If so, we're done...
        if (isset($_helpers[$helper]))
        {
            return TRUE;
        }
            
        if (file_exists($file . '.php'))
        {
            include_once $file . '.php';
            $_helpers[$helper] = TRUE;
            return TRUE;
        }
    
        return FALSE;
    }
}


if ( ! function_exists('recursionTree'))
{
    /**
     * 递归树
     *
     * @return void
     */
    function recursionTree($data, $node_templates) {
        $html = "";
    
        foreach ($data as $item) {
            $children = wd_array_val($item, 'children');
            if (empty($children)) {
                $template_file = wd_array_val($node_templates, 'leaf');
            }
            else {
                $template_file = wd_array_val($node_templates, 'parent');
            }
            $item['node_templates'] = $node_templates;
            $path = View::getInstance()->getCachePath();
            $html .= View::output($path . $template_file, $item, TRUE);
        }
    
        return $html;
    }
}


/**
 * URL构造
 *
 * @param string $c 控制器名称
 * @param string $a 方法名称
 * @param array  $q 参数数组
 * @param string $m 模块名称
 * @return string
 */
function wd_url($c, $a = NULL, array $q = NULL, $m = NULL) {
    $request = Wedo\Dispatcher::instance()->getRequest();
    if (! $m) {
        $m = $request->getModule();
    }

    if (! $a) {
        $a = Wedo\Dispatcher::instance()->getDefaultAction();
    }

    if (! $q) {
        $q = array();
    }

    $router = Wedo\Dispatcher::instance()->getRouter();
    $info = array(':m' => $m, ':c' => $c, ':a' => $a);
    $siteUri = $request->getBaseUri();
    if ($siteUri) {
        $url = rtrim($siteUri, '/') . $router->getCurrentRoute()->assemble($info, $q);
    }
    else {
        $url = $router->getCurrentRoute()->assemble($info, $q);
    }

    return $url;
}

/**
 * 取当前页方法的URL
 *
 * @param string $a 方法名
 * @param array  $q 参数
 * @return string
 */
function wd_controller_url($a = NULL, array $q = NULL) {
    $request = Wedo\Dispatcher::instance()->getRequest();
    $c = $request->getController();
    return wd_url($c, $a, $q);
}

/**
 * 获取当前的URL
 *
 * @return void
 * @author
 **/
function current_url()
{
    $request = Wedo\Dispatcher::instance()->getRequest();

    return controller_url($request->getMethod());
}
/**
 * 跳转到指定的URI
 *
 * @return void
 * @author
 **/
function redirect($uri = '', $method = 'location', $http_response_code = 302)
{
    switch($method)
    {
        case 'refresh'    : header("Refresh:0;url=".$uri);
        break;
        default     : header("Location: ".$uri, TRUE, $http_response_code);
        break;
    }
    exit;
}

/**
 * 生成唯一ID
 *
 * @param bool $show_hyphen 是否显示"-"符号
 * @return string 32位或36位的唯一ID
 */
function uuid($show_hyphen = FALSE) {
    //根据当前时间（微秒计）生成唯一id.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    if ($show_hyphen) {
        $hyphen = chr(45); // "-"
        $uuid = substr($charid, 0, 8) . $hyphen 
                . substr ($charid, 8, 4) . $hyphen 
                . substr ($charid, 12, 4) . $hyphen 
                . substr($charid, 16, 4) . $hyphen 
                . substr($charid, 20, 12);  
    }
    else {
        $uuid = $charid;
    }

    return $uuid;
}

function assets($uri) {
    $uri = wd_print('assets/{}', $uri); 
    return base_url($uri);
}

function theme_url($uri) {
    if (! defined('TEMPLATE')) {
        define('TEMPLATE', 'default');
    }

    if (! defined('THEME')) {
        define('THEME', 'default');
    }

    $uri = wd_print('templates/{}/{}/{}', TEMPLATE, THEME, $uri); 
    return base_url($uri);
}

function template_url($uri) {
    if (! defined('TEMPLATE')) {
        define('TEMPLATE', 'default');
    }
    $uri = wd_print('templates/{}/{}', TEMPLATE, $uri); 
    return base_url($uri);
}

function base_url($uri = '') {
    $baseUri = Wedo\Dispatcher::instance()->getRequest()->getBaseUri();
    if ($uri) {
        return rtrim($baseUri, '/') . '/' . ltrim($uri, '/');
    }

    return $baseUri;
}

/**
 * public目录
 *
 * @return string 返回public中的文件目录
 */
function public_path($file) {
    return PUBLIC_PATH . '/' . (($file) ? ltrim($file, '/') : '');
}

/**
 * 解析参数
 *
 * @param string $string 字符串
 * @param mixed  $params 参数
 * @return string 返回解析后结果
 */
function parse_string($string, $params = array()) {
    if (! $string || empty($params)) {
        return $string;
    }

    $string = preg_replace_callback(
        "#\{(.*)\}#iUs", 
        function($match) use (&$params) { 
            if (empty($match[1])) {
                if (!empty($params)) {
                    $val = array_shift($params);
                    if (is_array($val)) {
                        // $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                        $val = print_r($val, TRUE);
                    }

                    return $val;
                }

                return '{}';
            }

            return wd_array_val($params, $match[1], '{'.$match[1].'}'); 
        }, 
        $string
    );

    return $string;
}

/**
 * 格式化字符串
 *
 * @param string $str 格式化的字符串
 * @return string
 */
function wd_print($str) {
    $params  = func_get_args();
    if (count($params) > 1) {
        array_shift($params);
        $str = parse_string($str, $params);
    }

    return $str;
}

/**
 * 获得访客浏览器语言
 *
 * @return string
 */
function getClientLang(){
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $lang = substr($lang, 0, 5);
        if (preg_match("/zh-cn/i", $lang)) {
            $lang = "zh-cn";
        }
        else if (preg_match("/zh/i", $lang)) {
            // $lang = "繁体中文";
            $lang = "zh";
        }
        else {
            $lang = "en";
        }

        return $lang;
    }
    else {
        return "zh-cn";
    }
}

/**
 * 递归创建目录
 *
 * @param   string  $dir    目录名称
 * @return  bool|void
 */
function wd_mkdirs($dir) {
    if (! $dir) {
        return FALSE;    
    }

    if (! is_dir($dir)) {
        wd_mkdirs(dirname($dir));
        mkdir($dir, 0777);
    }
}

/**
 * 加载JS文件
 *
 * @param string $file   JS文件
 * @param string $module 模块名称，默认为当前模板
 * @return string
 */
function loadJs($file, $module = NULL) {
    $request = Wedo\Dispatcher::instance()->getRequest();
    if (! $module) {
        $module = $request->getModule();
    }

    if (ENVIRONMENT != 'production') {
        $url = wd_url('asset', 'index', NULL, 'index');        
        return wd_print('<script src="{}?m={}&f={}"></script>', $url, $module, $file);
    }

    // 生成缓存, 返回缓存的URL
    return '';
}

/**
 * 取客户端IP地址
 *
 * @return string
 */
function ip_address() {
    $request = Wedo\Dispatcher::instance()->getRequest();
    if (! $request) {
        return '0.0.0.0';
    }

    return $request->getIPAddress();
}

/**
 * Converts a human readable IP address to its packed in_addr representation
 *
 * @return string
 */
function ip_address_pton() {
    return inet_pton(ip_address());
}

/**
 * Encodes special characters into HTML entities.
 *
 * The {@link CApplication::charset application charset} will be used for encoding.
 * @param string $text data to be encoded
 * @return string the encoded data
 * @see http://www.php.net/manual/en/function.htmlspecialchars.php
 */
function encode_string($text)
{
    return htmlspecialchars($text, ENT_QUOTES, Wedo\Application::app()->charset);
}

/**
 * Decodes special HTML entities back to the corresponding characters.
 * This is the opposite of {@link encode()}.
 * @param string $text data to be decoded
 * @return string the decoded data
 * @see http://www.php.net/manual/en/function.htmlspecialchars-decode.php
 * @since 1.1.8
 */
function decode_string($text)
{
    return htmlspecialchars_decode($text, ENT_QUOTES);
}

/**
 * 处理sql语句
 *
 * @param string $sql 原始的sql
 * @return array 
 */
function wd_split_sql($sql) {
    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesArr = explode(";\n", trim($sql));
    unset($sql);
    foreach ($queriesArr as $querys) {
        $queries = explode("\n", trim($querys));
        foreach ($queries as $query) {
            $query = trim($query);
            $val = (starts_with($query, '#') || starts_with($query, '--')) ? NULL : $query;
            if (isset($ret[$num])) {
                $ret[$num] .= $val;
            } else {
                $ret[$num] = $val;
            }
        }

        $num++;
    }

    return $ret;
}