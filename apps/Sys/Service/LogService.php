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
use Apps\Sys\Entity\Log;
use Apps\Sys\Models\LogModel;
use Wedo\Dispatcher;

class LogService
{
    /**
     * 添加操作日志
     *
     * @param string  $logKey      日志Key
     * @param string  $description 操作描述
     * @param integer $uid         操作人ID
     * @return mixed
     */
    public static function writeLog($logKey, $description = NULL, $uid = NULL) {
        $uid = $uid ? $uid : UserService::getLoginUid();
        $log = Log::create();
        $log->setLogKey($logKey);
        $log->setDescription($description);
        $log->setUid($uid ? $uid : 0);
        $log->setIpAddress(ip_address_pton());
        $log->setCreateAt(time());

        $request = Dispatcher::instance()->getRequest();
        if ($request) {
            $log->setRequestUuid($request->getUUID());
            $browser = $request->getBrowser();
            if ($browser) {
                $log->setBrowserType($browser->getName() . ' ' . $browser->getVersion());
                $log->setOs($browser->getPlatform());
                $log->setDevice($browser->getDevice());
            }
        }

        return LogModel::instance()->addEntity($log);
    }
}