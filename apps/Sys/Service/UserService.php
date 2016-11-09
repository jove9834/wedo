<?php
/**
 * <<文件说明>>
 *
 * @author        黄文金
 * @copyright  Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link            http://weidu178.com
 * @since          Version 1.0
 */
namespace Apps\Sys\Service;
use Apps\Sys\Entity\LoginUser;
use Apps\Sys\Entity\User;
use Apps\Sys\Entity\UserAccountIndex;
use Apps\Sys\Models\UserAccountIndexModel;
use Apps\Sys\Models\UserModel;
use Apps\Sys\Models\UserProfileModel;
use Wedo\Cache\Cache;
use Wedo\Config;
use Wedo\Logger;

/**
 * Class UserService
 * @package Apps\Sys\Service
 */
class UserService
{
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
     * @return User 返回登录信息
     */
    public static function login($account, $password) {
        if (! $account || ! $password) {
            throw new \Exception(lang('帐号、密码不能为空'), 1);
        }

        if(static::isTimeLocked($account)) {
            // 帐号登录出错次数超过最大次数，被锁定
            throw new \Exception(wd_print(lang('登录出错次数超过{}次，帐号已被锁定'), self::MAXIMUM_LOGIN_ATTEMPTS), 5);
        }

        $user = UserModel::instance()->getUserByAccount($account);
        if (! $user) {
            // 帐号不存在
            $msg = lang('帐号不存在');
            LogService::writeLog('login', wd_print('error:{}, account:{}', $msg, $account));
            throw new \Exception(lang('帐号或密码不正确'), 2);
        }

        // 判断用户密码
        $hashPassword = static::hashPassword($password, $user->getSalt());
        Logger::debug('hashPassword:{}', $hashPassword);
        if (strcasecmp($hashPassword, $user->getPassword()) != 0) {
            // 密码不正确
            self::increaseLoginAttempts($account);
            $msg = lang('密码不正确');
            LogService::writeLog('login', wd_print('error:{}, account:{}', $msg, $account));
            throw new \Exception(lang('帐号或密码不正确'), 2);
        }

        // 判断用户状态
        if ($user->isDel()) {
            // 帐号已删除
            throw new \Exception(lang('帐号已删除'), 3);
        }

        if ($user->isActive()) {
            // 帐号未激活
            throw new \Exception(lang('帐号未激活'), 4);
        }

        $loginUser = LoginUser::create();
        $loginUser->setUid($user->getUid());
        $loginUser->setName($user->getName());
        $loginUser->setGender($user->getGender());
        $loginUser->setAvatar($user->getAvatar());

        // 生成CookieKey
        self::saveSession(self::getCookieCode(), $loginUser);

        // 写日志
        LogService::writeLog('login', $account, $user->getUid());
        // 清除登录尝试记录
        self::clearLoginAttempts($account);
        return $user;
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
            LogService::writeLog('logout', NULL, $user->getUid());
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
     * @return LoginUser|boolean
     */
    public static function getLoginUser() {
        // 取得cacheKey
        $data = Cache::get(self::getCookieCode());
        if ($data) {
            return LoginUser::fromJson($data);
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
            self::$loginUid = $loginUser ? $loginUser->getUid() : FALSE;
        }

        return self::$loginUid;
    }

    /**
     * 创建帐号
     *
     * @param string  $account  帐号
     * @param string  $password 密码
     * @param integer $type     帐号类型
     * @param User    $info     用户信息
     * @return bool 1.帐号不能为空 2.帐号已存在 3.帐号已被删除 4.帐号未激活, 5.帐号被锁定
     * @throws \Exception 1.帐号不能为空 2.帐号已存在 3.帐号已被删除 4.帐号未激活, 5.帐号被锁定
     */
    public static function createAccount($account, $password = NULL, $type = UserAccountIndex::ACCOUNT_TYPE_EMAIL, User $info = NULL) {
        if (! $account) {
            throw new \Exception(lang('帐号不能为空'), 1);
        }

        // 判断帐号是否存在
        if (static::exists($account)) {
            throw new \Exception(lang('帐号已存在'), 2);
        }

        $info == NULL && $info = User::create();
        $info->setPassword($password);
        $info->setCreateAt(time());
        // 对密码进行加密
        $info->hashPassword();
        $uid = UserModel::instance()->addEntity($info);

        if (! $uid) {
            return FALSE;
        }

        // 添加帐号
        try {
            UserModel::instance()->addAccount($uid, $account, $type);
            // 写日志
            LogService::writeLog('create_account', '创建帐号', $uid);
            return TRUE;
        } catch (\Exception $e) {
            LogService::writeLog('create_account', wd_print('创建帐号出现异常， 异常信息：{}', $e->getMessage()), $uid);
        }

        return FALSE;
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

        return UserAccountIndexModel::instance()->existsAccount($account);
    }

    /**
     * 修改密码
     *
     * @param string $uid      用户ID
     * @param string $password 新的密码
     * @throws \Exception 1.帐号不存在
     * @return boolean
     */
    public static function updatePassword($uid, $password) {
        return UserModel::instance()->updatePassword($uid, $password);
    }

    /**
     * 保存登录状态至Session， 注意，这里的session可以是缓存如redis
     *
     * @param string    $cookieCode cookie代码
     * @param LoginUser $user       用户数据
     * @return void
     */
    private static function saveSession($cookieCode, LoginUser $user) {
        self::$loginUid = $user->getUid();
        Cache::set($cookieCode, $user->toJson(TRUE), self::CACHE_EXPIRE);
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
        } else {
            setcookie('__USER__', $code, time() + self::CACHE_EXPIRE, '/');
        }

        return $code;
    }

    /**
     * 密码加密
     *
     * @param string $password 明文密码
     * @param string $salt     加密代码
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
     * @param int $len 生成的随机代码长度，默认为6位
     * @return string
     */
    public static function generateSalt($len = 6) {
        return substr(md5(uniqid(rand(), true)), 0, $len);
    }

    /**
     * 帐号是否超过尝试登录次数被锁定
     *
     * @param string $account 帐号
     * @return boolean
     */
    private static function isTimeLocked($account) {
        // 取timelocked key
        $key = self::getTimelockedCacheKey($account);
        $timeLocked = Cache::get($key);
        return $timeLocked == 1;
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
            // 超过次数，锁定，锁定24小时
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
        if ($name) {
            return UserProfileModel::instance()->getValue($uid, $name);
        }

        return UserProfileModel::instance()->getProfile($uid);
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
        return UserProfileModel::instance()->setProfile($uid, $name, $value);
    }

    /**
     * 删除用户属性
     *
     * @param integer $uid  用户ID, 为空时默认为当前登录用户ID
     * @param string  $name 属性名称
     * @return integer
     */
    public static function deleteUserProfile($uid = NULL, $name = NULL) {
        $uid = $uid ?: self::getLoginUid();
        return UserProfileModel::instance()->deleteProfile($uid, $name);
    }

    /**
     * 增加帐号索引
     *
     * @param integer $uid     用户ID
     * @param string  $account 帐号
     * @param integer $type    帐号类型
     * @return bool
     */
    public static function addAccountIndex($uid, $account, $type = NULL) {
        if (! $account || ! $uid) {
            return FALSE;
        }

        return UserAccountIndexModel::instance()->addAccount($uid, $account, $type);
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

        return UserAccountIndexModel::instance()->deleteByAccount($account);
    }
}