<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/7/2
 * Time: 下午1:55
 */

namespace Business;

use Core\Base\Business;
use Core\Tools\ErrorHandler;
use Library\Common;
use Models\UserLoginLogModel;
use Phalcon\Di;
use Wrappers\UserLoginLogWrapper;

class LogBusiness extends Business
{
    public function saveLoginLog($uid, $loginType, $deviceId)
    {
        if(empty($loginType)){
            ErrorHandler::setErrorInfo("登录类型错误", -1);
            return false;
        }
        if(empty($uid)){
            ErrorHandler::setErrorInfo("登录信息异常", -1);
            return false;
        }
        //验证设备号是否被禁用

        //退出之前登录的账号
        $logoutRet = $this->setLogout($uid);
        if(!$logoutRet){
            ErrorHandler::setErrorInfo("系统繁忙，请稍后重试", -1);
            return false;
        }

        $mUserLoginLog = new UserLoginLogModel();
        $data = [
            'uid'=>$uid,
            'token'=>uuid_create() . $uid,
            'type'=>$loginType,
            'description'=>'',
            'sign_out'=>0,
            'login_time'=>date('Y-m-d H:i:s'),
            'login_ip'=>Common::getClientIP(),
            'device_id'=>$deviceId
        ];
        $userLoginLogWrapper = new UserLoginLogWrapper($data);
        $userLoginLogWrapper->mappingToModel($mUserLoginLog);
        $mUserLoginLog->create();
    }

    public function setLogout($uid, $updateCache = TRUE)
    {
        $mUserLoginLog = new UserLoginLogModel();
        $ret = $mUserLoginLog->logout($uid);
        if($updateCache){
            $mUserLoginLog->clearLoginCache($uid);
        }
        return $ret;
    }
}