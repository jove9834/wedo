<?php
/**
 * 帐号提供的API接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */

namespace Apps\Sys\Utils;

use Wedo\Cache\Cache;
use Wedo\Config;
use Wedo\Logger;
use Apps\Sys\Models\UserModel;
use Apps\Sys\Models\UserProfileModel;
use Apps\Sys\Models\UserAccountIndexModel;
use Apps\Sys\Models\ActivationCodeModel;

class User {
    /**
     * 加密代码长度
     *
     * @var integer
     */
    const SALT_LENGTH = 6; 
    
    /**
     * 锁定时间，单位秒
     *
     * @var integer
     */
    const LOCKED_TIME = 300;

    /**
     * 缓存有效期, 30天
     *
     * @var integer
     */
    const CACHE_EXPIRE = 2592000; // 为空表示无限期

    /**
     * 最大尝试登录次数
     *
     * @var integer
     */
    const MAXIMUM_LOGIN_ATTEMPTS = 5;

    /**
     * COOKIE验证串
     *
     * @var string
     */
    const COOKIE_SECURITY_CODE = '3fcf273f383d324ccb05aaec9fcc0ec7a0a2a67e';

    /**
     * cookie code
     *
     * @var string
     */
    static $cookieCode = NULL;
    /**
     * 登录用户ID
     *
     * @var integer
     */
    static $loginUid = NULL;

    /**
     * 登录处理
     *
     * @param string $account  帐号
     * @param string $password 密码
     * @throws \Exception 1.帐号和密码不能为空 2.帐号或密码错误 3.帐号已被删除 4.帐号未激活, 5.帐号被锁定
     * @return boolean 返回TRUE，登录成功，否则为FALSE     
     */
    public static function login($account, $password) {
        if (! $account || ! $password) {
            throw new \Exception(lang('帐号、密码不能为空'), 1);
        }

        if(static::isTimeLocked($account)) {
            // 帐号登录出错次数超过最大次数，被锁定
            throw new \Exception(lang('登录出错次数超过5次，帐号已被锁定'), 5);
        }
        
        $ip_address = ip_address_pton();

        $userModel = new UserModel();
        $user = $userModel->getUserByAccount($account);
        if (! $user) {
            // 帐号不存在
            $msg = lang('帐号不存在');
            LogApi::writeLog(CURRENT_FUNCTION, 'login', wd_print('error:{}, account:{}, IP:{}', $msg, $account, $ip_address));
            throw new \Exception(lang('帐号或密码不正确'), 2);            
        }

        // 判断用户密码
        $hashPassword = static::hashPassword($password, $user['salt']);
        Logger::debug('hashPassword:{}', $hashPassword);
        if (strcasecmp($hashPassword, $user['password']) != 0) {
            // 密码不正确
            self::increaseLoginAttempts($account);
            $msg = lang('密码不正确');
            LogApi::writeLog(CURRENT_FUNCTION, 'login', wd_print('error:{}, account:{}, IP:{}', $msg, $account, $ip_address));
            throw new \Exception(lang('帐号或密码不正确'), 2);     
        }

        // 判断用户状态        
        if ($user['is_del'] == '1') {
            // 帐号已删除
            throw new \Exception(lang('帐号已删除'), 3);   
        }

        if ($user['is_active'] != '1') {
            // 帐号未激活
            throw new \Exception(lang('帐号未激活'), 4);   
        }

        // 写session
        $sessionData = array(
            'uid' => $user['uid'],
            'email' => $user['email'],
            'name' => $user['name'],
            'gender' => $user['gender'],
            'avatar' => $user['avatar'],
            );

        // 生成CookieKey
        self::saveSession(self::getCookieCode(), $sessionData);

        // 写日志
        LogApi::writeLog(CURRENT_FUNCTION, 'login', $account, $user['uid']);
        // 清除登录尝试记录
        self::clearLoginAttempts($account);
        return $sessionData;
    }

    /**
     * 退出登录
     *
     * @return void
     */
    public static function logout() {
        $user = self::getLoginUser();
        self::destorySession();
        if ($user) {
            // 写日志
            LogApi::writeLog(CURRENT_FUNCTION, 'logout', NULL, $user['uid']);    
        }        
    }

    /**
     * 判断是否登录状态
     *
     * @return boolean      
     **/
    public static function isLogined() {
        $uid = self::getLoginUid();

        return $uid ? TRUE : FALSE;
    }

    /**
     * 取登录用户信息
     *
     * @return array|boolean
     */
    public static function getLoginUser() {
        // 取得cacheKey
        $data = Cache::get(self::getCookieCode());
        if ($data) {
            $data = json_decode($data, TRUE);
            return $data;
        }
        else {
            // self::destorySession();
            return FALSE;
        }
    }

    /**
     * 取当前登录用户ID
     *
     * @return integer|boolean
     */
    public static function getLoginUid() {        
        if (! self::$loginUid) {
            $loginUser = static::getLoginUser();
            self::$loginUid = $loginUser ? wd_array_val($loginUser, 'uid') : FALSE;            
        }

        return self::$loginUid;
    }

    /**
     * 创建帐号
     *
     * @param string $email     帐号
     * @param string $password  密码
     * @param mixed  $otherInfo 其他信息
     * @throws Exception 1.帐号不能为空 2.帐号已存在 3.帐号已被删除 4.帐号未激活, 5.帐号被锁定
     * @return boolean 返回TRUE，登录成功，否则为FALSE     
     */
    public static function createAccount($email, $password = NULL, $otherInfo = NULL) {
        if (! $email) {
            throw new \Exception(lang('帐号不能为空'), 1);
        }

        // 判断帐号是否存在
        if (static::exists($email)) {
            throw new \Exception(lang('帐号已存在'), 2);
        }

        $data = [];
        if ($otherInfo && is_array($otherInfo)) {
            $data = $otherInfo;
        }

        // 邮箱地址
        $data['email'] = $email;
        // 生成salt
        $salt = self::generateSalt();
        $data['salt'] = $salt;
        if ($password) {
            $hashPassword = static::hashPassword($password, $salt);
            $data['password'] = $hashPassword;
        }

        // 默认设置
        $data['reg_ip'] = ip_address_pton();
        // 创建时间
        $data['ctime'] = time();

        $userModel = new UserModel();
        $uid = $userModel->add($data);
        if (! $uid) {
            return FALSE;
        }

        // 写日志
        LogApi::writeLog(CURRENT_FUNCTION, 'create_account', '新建帐号', $uid);

        return TRUE;
    }

    /**
     * 判断帐号是否存在
     *
     * @param string $account 帐号
     * @return boolean
     */
    public static function exists($account) {
        if (! $account) {
            // 帐号不能为空，否则当存在
            return TRUE;
        }

        $userAccountIndexModel = new UserAccountIndexModel();
        return $userAccountIndexModel->exists($account);
    }

    /**
     * 修改密码
     *
     * @param string $uid      用户ID
     * @param string $passowrd 密码
     * @throws Exception 1.帐号不存在
     * @return boolean
     */
    public static function updatePassword($uid, $passowrd) {
        $userModel = new UserModel();
        $user = $userModel->getUser($uid);
        if (! $user) {
            // 帐号不存在
            throw new \Exception(lang('帐号不存在'), 1);            
        }

        $hashPassword = static::hashPassword($password, $user['salt']);
        return $userModel->update($uid, array('password' => $hashPassword));
    }

    /**
     * 保存登录状态至Session， 注意，这里的session可以是缓存如redis
     *
     * @param string $cookieCode cookie代码
     * @param array  $userData   用户数据
     * @return void
     */
    private static function saveSession($cookieCode, array $userData) {
        self::$loginUid = wd_array_val($userData, 'uid');       
        $data = json_encode($userData, JSON_UNESCAPED_UNICODE);
        Cache::set($cookieCode, $data, self::CACHE_EXPIRE);
    }

    /**
     * 注销Session
     *
     * @return void
     */
    private static function destorySession() {
        $code = self::getCookieCode();
        Cache::delete($code);
        self::$cookieCode = NULL;        
        self::$loginUid = NULL;
    }

    /**
     * 获取COOKIE保存的CODE
     * 
     * @param boolean $force 当CODE为空时是否强制刷新
     * @return string 32位的MD5 KEY
     */
    private static function getCookieCode($force = FALSE) {
        if ($force) {
            // 强制生成cookieCode
            self::$cookieCode = self::generateUserCookie();            
        }

        if (! self::$cookieCode) {
            self::$cookieCode = is_array($_COOKIE) && isset($_COOKIE['__USER__']) ? $_COOKIE['__USER__'] : '';
            // 如果CODE为空，则再去生成下
            if (! self::$cookieCode) {
                self::$cookieCode = self::generateUserCookie();
            }
        }
        
        return self::$cookieCode;
    }

    /**
     * 设置USER登陆信息
     *
     * @return string 返回CODE
     */
    private static function generateUserCookie() {
        $code = md5(self::COOKIE_SECURITY_CODE . '-' . microtime() . '-' . mt_rand());
        $cookie_domain = Config::get('cookie_domain');
        if ($cookie_domain) {
            setcookie('__USER__', $code, time() + self::CACHE_EXPIRE, '/', $cookie_domain);
        }
        else {
            setcookie('__USER__', $code, time() + self::CACHE_EXPIRE, '/');   
        }
        
        return $code;
    }

    /**
     * 密码加密
     *
     * @param string 明文密码
     * @param string 加密代码
     * @return string 返回加密后的字符串
     */
    public static function hashPassword($password, $salt = '') {
        if (empty($password)) {
            return FALSE;
        }

        return sha1($password . $salt);
    }

    /**
     * 生成随机的加密代码.
     *
     * @return string 
     */
    public static function generateSalt() {
        return substr(md5(uniqid(rand(), true)), 0, self::SALT_LENGTH);
    }

    /**
     * 帐号是否超过尝试登录次数被锁定
     *
     * @param string $account 帐号
     * @return void
     */
    private static function isTimeLocked($account) {
        // 取timelocked key
        $key = self::getTimelockedCacheKey($account);
        $timelocked = Cache::get($key);
        return $timelocked == 1;
    }

    /**
     * 增加尝试登录记录
     *
     * @param string $account 帐号
     * @return void
     */
    private static function increaseLoginAttempts($account) {
        if (self::MAXIMUM_LOGIN_ATTEMPTS <= 0) {
            return;
        }

        $key = self::getLoginAttemptCacheKey($account);
        $attempts = Cache::get($key);
        $attempts = $attempts ? $attempts + 1 : 1;
        if ($attempts >= self::MAXIMUM_LOGIN_ATTEMPTS) {
            // 超过次数，锁定
            Cache::set(self::getTimelockedCacheKey($account), 1, 86400);
            // 清除尝试记录
            self::clearLoginAttempts($account);
        }
        else {
            Cache::set($key, $attempts, 86400);    
        }
    }

    /**
     * 清除登录尝试记录
     *
     * @param string $account 帐号
     * @return void
     */
    private static function clearLoginAttempts($account) {
        $key = self::getLoginAttemptCacheKey($account);
        Cache::delete($key);
    }

    /**
     * 是否已超过最大登录尝试次数
     *
     * @param string $account 帐号
     * @return void
     */
    private static function isMaxLoginAttemptsExceeded($account) {
        if (self::MAXIMUM_LOGIN_ATTEMPTS > 0) {
            $key = self::getLoginAttemptCacheKey($account);
            $attempts = Cache::get($key);
            return $attempts && $attempts >= self::MAXIMUM_LOGIN_ATTEMPTS;
        }

        return FALSE;
    }

    /**
     * 获取尝试登录记录缓存Key
     *
     * @param string $account 帐号
     * @return string
     */
    private static function getLoginAttemptCacheKey($account) {
        $ip_address = ip_address_pton();
        return md5($account . '_' . $ip_address);
    }

    /**
     * 获取尝试登录记录缓存Key
     *
     * @param string $account 帐号
     * @return string
     */
    private static function getTimelockedCacheKey($account) {
        $ip_address = ip_address_pton();
        return md5($account . '_' . $ip_address . '_timelocked');
    }

    /**
     * 取用户属性
     *
     * @param integer $uid  用户ID, 为空时默认为当前登录用户ID
     * @param string  $name 属性名称，为空时，取所有属性，返回数组
     * @return array|string 当$name为空时，返回一维array，否则返回属性值
     */
    public static function getUserProfile($uid = NULL, $name = NULL) {
        $uid = $uid ?: self::getLoginUid();
        $userProfileModel = new UserProfileModel();
        if ($name) {
            return $userProfileModel->getValue($uid, $name);    
        }

        return $userProfileModel->getProfile($uid);        
    }

    /**
     * 设置用户属性
     *     
     * @param integer $uid   用户ID, 为空时默认为当前登录用户ID
     * @param string  $name  属性名称
     * @param string  $value 属性值     
     * @return boolean
     */
    public static function setUserProfile($uid, $name, $value) {
        $uid = $uid ?: self::getLoginUid();
        $userProfileModel = new UserProfileModel();
        return $userProfileModel->setProfile($uid, $name, $value);
    }

    /**
     * 删除用户属性
     *     
     * @param integer $uid  用户ID, 为空时默认为当前登录用户ID
     * @param string  $name 属性名称   
     * @return boolean
     */
    public static function deleteUserProfile($uid = NULL, $name = NULL) {
        $uid = $uid ?: self::getLoginUid();
        $userProfileModel = new UserProfileModel();
        return $userProfileModel->deleteProfile($uid, $name);
    }

    /**
     * 增加帐号索引
     *
     * @param integer $uid     用户ID
     * @param string  $account 帐号
     * @return boolean
     */
    public static function addAccountIndex($uid, $account) {
        if (! $account || ! $uid) {
            return FALSE;
        }

        $userAccountIndexModel = new UserAccountIndexModel();
        return $userAccountIndexModel->addAccount($account, $uid);
    }

    /**
     * 删除帐号索引
     *
     * @param string $account 帐号
     * @return integer|boolean 删除记录数或FALSE
     */
    public static function deleteAccountIndex($account) {
        if (! $account) {
            return FALSE;
        }

        $userAccountIndexModel = new UserAccountIndexModel();
        return $userAccountIndexModel->deleteAccount($account);
    }
}