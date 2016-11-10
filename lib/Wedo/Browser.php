<?php
/**
 * 浏览器信息
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

/**
 * 浏览器信息
 */
class Browser {
    /**
     * 浏览器名称
     *
     * @var string
     */
    private $name;

    /**
     * 浏览器版本
     *
     * @var string
     */
    private $version;

    /**
     * 用户所在系统平台
     *
     * @var string
     */
    private $platform;

    /**
     * 用户接口识别的字符串，通过$_SERVER['HTTP_USER_AGENT']变量获得
     *
     * @var string
     */
    private $userAgent;

    /**
     * 终端设备类型
     *
     * @var string
     */
    private $device;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->init();
    }

    /**
     * 调用父类的初始化方法，然后检测用户的浏览信息
     *
     * @return void
     */
    public function init() {
        $this->detect();
    }

    /**
     * 通过$_SERVER['HTTP_USER_AGENT']检测用户浏览信息，分别赋值于四个私有变量
     *
     * @return void
     */
    protected function detect() {
        $detect = new MobileDetect();
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (preg_match('/opera/', $userAgent)) {
            $name = 'Opera';
        } elseif (preg_match('/chrome/', $userAgent)) {
            $name = 'Chrome';
        } elseif (preg_match('/apple/', $userAgent)) {
            $name = 'Safari';
        } elseif (preg_match('/msie/', $userAgent)) {
            $name = 'IE';
        } elseif (preg_match('/mozilla/', $userAgent) && !preg_match('/compatible/', $userAgent)) {
            $name = 'Mozilla';
        } else {
            $name = 'unrecognized';
        }

        if (preg_match('/linux/', $userAgent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/', $userAgent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/', $userAgent)) {
            $platform = 'windows';
        } else {
            $platform = 'unrecognized';
        }

        $this->name = $name;
        $this->version = $detect->version($name);
        $this->platform = $platform;
        $this->userAgent = $userAgent;
        $this->device = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
    }

    /**
     * 获取浏览器名称
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * 获取浏览器版本
     *
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * 获取用户系统
     *
     * @return string
     */
    public function getPlatform() {
        return $this->platform;
    }

    /**
     * 获取用户代理接口信息
     *
     * @return string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * 获取设备类型
     *
     * @return string
     */
    public function getDevice() {
        return $this->device;
    }
}
