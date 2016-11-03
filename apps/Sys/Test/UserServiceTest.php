<?php
/**
 * Created by PhpStorm.
 * User: huangwj
 * Date: 2016/11/3
 * Time: 下午10:03
 */

namespace Apps\Sys\Test;


use Apps\Sys\Service\UserService;
use Wedo\Logger;
use Wedo\TestCase;

class UserServiceTest  extends TestCase
{
    /**
     * 标题
     *
     * @var string
     */
    protected $suiteTitle = "UserService测试用例";

    /**
     * 登录测试
     */
    public function testingLogin() {
        $loginUser = UserService::login('admin', '88888');
        Logger::debug(json_encode($loginUser));
    }
}