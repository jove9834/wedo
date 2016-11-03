<?php
/**
 * 测试用例
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo;

use Testify\Testify;
use ReflectionClass;
use ReflectionMethod;

/**
 * 测试用例
 */
class TestCase extends Testify
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 运行测试类
     *
     * @param string $class
     * @return Testify
     */
    public function run($class = NULL)
    {
        $reflection = new ReflectionClass($class);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $m) {
            if (! starts_with($m->name, 'testing')) {
                continue;
            }

            $closure = $m->getClosure($this);
            $this->test($m->name, $closure);
        }

        return parent::run();
    }

}