<?php
/**
 * 响应
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

use Wedo\Logger;

/**
 * 响应
 */
class Response {
    /**
     * 输出JSON格式
     *
     * @return void
     */
    public static function toJson($data) {
        self::print(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 输出文本
     *
     * @return void
     */
    public static function print($text) {
        echo $text;
    }
}