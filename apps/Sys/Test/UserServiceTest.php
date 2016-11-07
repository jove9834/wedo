<?php
/**
 * Created by PhpStorm.
 * User: huangwj
 * Date: 2016/11/3
 * Time: 下午10:03
 */

namespace Apps\Sys\Test;


use Apps\Sys\Entity\User;
use Apps\Sys\Entity\UserAccountIndex;
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
    protected $suiteTitle = "UserService Testing";

    /**
     * 登录测试
     */
    public function testingLogin() {
        try {
            UserService::login('admin', '888888');
            $this->assertTrue(TRUE);
            $loginUser = UserService::getLoginUser();
            Logger::debug($loginUser);
            $this->assertTrue(TRUE);
        } catch (\Exception $e) {
            $this->assertTrue(FALSE);
        }
    }

    public function testingCreateAccount() {
        try {
            UserService::createAccount('admin', '888888', UserAccountIndex::ACCOUNT_TYPE_USERNAME);
            $this->assertTrue(TRUE);
        } catch (\Exception $e) {
            $this->assertTrue(FALSE);
        }
    }

    public function testingUpdatePassword() {
        try {
            UserService::updatePassword(1, '111111');
            $this->assertTrue(TRUE);
        } catch (\Exception $e) {
            $this->assertTrue(FALSE);
        }
    }

    public function testingEntity() {
        $user = User::create();
        $user->setUid(1);
        $user->setName('admin');
        $s = $user->toJson(TRUE);
        Logger::debug($s);

        // 转换为实体
        $user1 = User::fromJson($s);
        Logger::debug($user1);
        $this->assertTrue(TRUE);
    }
}