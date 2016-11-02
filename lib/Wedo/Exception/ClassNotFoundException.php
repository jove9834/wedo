<?php
/**
 * ClassNotFoundException class file.
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Exception;

/**
 * Class Not Found Exception.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.base
 * @since 1.0
 */
class ClassNotFoundException extends CException {
    /**
     * Not found class name
     *
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param string $class Not found class name
     */
    public function __construct($class)
    {
        $this->class = $class;
        parent::__construct("Class Not Found Exception : " . $class);
    }
}

