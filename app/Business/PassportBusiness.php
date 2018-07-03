<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/6/29
 * Time: 下午1:26
 */

namespace Business;

use Core\Base\Business;
use Core\Tools\ErrorHandler;
use Enumerations\UserConst;
use Library\Common;
use Models\UserModel;
use Phalcon\Di;

class PassportBusiness extends Business
{
    public function login($username, $password)
    {
        if(empty($username)){
            ErrorHandler::setErrorInfo("请输入账号", -1);
            return false;
        }
        if(empty($password)){
            ErrorHandler::setErrorInfo("请输入密码", -1);
            return false;
        }

        $mUser = new UserModel();
        $loginUser = $mUser->getInfoByUser($username);
        if(empty($loginUser)){
            ErrorHandler::setErrorInfo("请输入正确的账号", -1);
            return false;
        }

        if($loginUser['status'] == UserConst::STATUS_NO_ACTIVE){
            ErrorHandler::setErrorInfo("请前往邮件激活账号", -1);
            return false;
        }
        if($loginUser['status'] == UserConst::STATUS_BAN){
            ErrorHandler::setErrorInfo("账号已被禁用", -1);
            return false;
        }
        if($loginUser['status'] != UserConst::STATUS_NORMAL){
            ErrorHandler::setErrorInfo("账号错误", -1);
            return false;
        }
        //检查是否被禁用-密码错误过多
        if($mUser->existBanKey($loginUser['id'])){
            ErrorHandler::setErrorInfo("账号被锁定，请一个小时后重试！", -1);
            return false;
        }
        if(!$mUser->preventCache($loginUser['id'])){
            ErrorHandler::setErrorInfo("账号被锁定，请一个小时后重试！", -1);
            return false;
        }
        $oriPassword = $loginUser['password'];
        if($oriPassword != Common::pwdEncode($password, $loginUser['salt'])){
            ErrorHandler::setErrorInfo("账号密码错误", -1);
            return false;
        }
        $mUser->clearLoginCache($loginUser['id']);
        //获取token
        $bLog = new LogBusiness();
        $bLog->saveLoginLog($loginUser['id'], 1, "12");
        Di::getDefault()->get('session')->set('loginUser', $loginUser);
        return $loginUser;
    }
}