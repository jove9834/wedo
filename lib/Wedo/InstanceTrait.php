<?php
/**
 * <<文件说明>>
 *
 * @author        黄文金
 * @copyright  Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link            http://weidu178.com
 * @since          Version 1.0
 */
namespace Wedo;
trait InstanceTrait
{
    /**
     * @var static
     */
    private static $instances;

    /**
     * 单例模式
     *
     * @return static
     */
    public static function instance() {
        $c = get_called_class();
        if (! isset(self::$instances[$c])) {
            self::$instances[$c] = new static;
        }

        return self::$instances[$c];
    }
}