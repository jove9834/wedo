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

/**
 * 响应
 */
class Response {
    /**
     * 输出JSON格式
     *
     * @param array $data 要输出的数组
     */
    public static function toJson(array $data) {
        self::echoText(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 输出文本
     *
     * @param string $text 输出的文本
     */
    public static function echoText($text) {
        echo $text;
    }
}