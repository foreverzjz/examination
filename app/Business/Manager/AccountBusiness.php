<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2018/4/12
 * Time: 下午1:13
 */

namespace Business\Manager;

use Core\Base\Business;
use Core\Tools\ErrorHandler;
use Core\Tools\StringUtil;
use Core\Tools\ClientInfo;
use Library\Common;
use Models\ManagerModel;
use Models\ManagerSignLogModel;
use Models\UserLoginLogModel;
use Models\UserModel;
use Wrappers\ManagerAuthWrapper;
use Wrappers\ManagerWrapper;

class AccountBusiness extends Business
{
    const ACCOUNT_STATUS_NORMAL = 1;
    const ACCOUNT_STATUS_DELETE = 0;
    const AUTH_SESSION_KEY = 'auth_manager';

    private function saltPassword(string $password, string $salt): string
    {
        $sha1Word = sha1($salt . $password);
        return md5(substr($sha1Word, 10, 20) . substr($sha1Word, 0, 10) . substr($sha1Word, 30, 10));
    }

    public static function getAuth()
    {
        $session = \Phalcon\Di::getDefault()->get('session');
        return $session->get(self::AUTH_SESSION_KEY);
    }

    protected function setAuth(ManagerWrapper $manager)
    {
        parent::getSession();
        $wrapAuth = ManagerAuthWrapper::getInstance($manager->toArray());
//        $bizRole = new RoleBusiness();
//        $roleInfo = $bizRole->rolePower($manager->roles);
//        $wrapAuth->menu = $roleInfo['menu'];
//        $wrapAuth->route = $roleInfo['route'];
//        $wrapAuth->role = $roleInfo['name'];
        $this->session->set(self::AUTH_SESSION_KEY, $wrapAuth->toArray());
    }

    /**
     * 注销登录认证信息
     * @param $managerId
     */
    public function cancelAuth($managerId)
    {
        $mManagerSign = new UserLoginLogModel();
        $signInList = $mManagerSign->fetchRecords("uid={$managerId} AND sign_out = 0");
        $mManagerSign->logout($managerId);
        $this->getRedis();
        if ($signInList) {
            foreach ($signInList as $value) {
                $this->redis->delete("_PHCR{$this->config->redisSessionAdapter->prefix}{$value['login_id']}");
            }
        }
    }

    public function login(string $account, string $password)
    {
        $mUser = new UserModel();
        $manager = $mUser->fetchOneRecord("mp = '{$account}' OR username = '{$account}'");
        if (!$manager) {
            ErrorHandler::setErrorInfo('帐号或密码错误！');
            return FALSE;
        }

        if ($manager['password'] != Common::pwdEncode($password, $manager['salt'])) {
            ErrorHandler::setErrorInfo('管理帐号或登录密码错误！');
            return FALSE;
        }
        $this->cancelAuth($manager['id']);
        $this->getSession();

        $loginTime = date('Y-m-d H:i:s');
        $mUserLoginLog = new UserLoginLogModel();
        $token = Common::uuid_generate();
        $data = [
            'uid'=>$manager['id'],
            'token'=>$token,
            'type'=>1,
            'description'=>'',
            'sign_out'=>0,
            'login_time'=>$loginTime,
            'update_time'=>$loginTime,
            'login_ip'=>Common::getClientIP(),
            'device_id'=>""
        ];
        $result = $mUserLoginLog->recordLoginLog($data);
        if (!$result) {
            ErrorHandler::setErrorInfo('登录失败！');
            return FALSE;
        }

        $wrapManager = new ManagerWrapper($manager);
        $this->setAuth($wrapManager);
        return TRUE;
    }

    public function logout()
    {
        $this->getSession();
        $auth = $this->session->get(self::AUTH_SESSION_KEY);
        $this->session->remove(self::AUTH_SESSION_KEY);
        $this->cancelAuth($auth['id']);
    }

    public function create(ManagerWrapper $wrapManager)
    {
        $mManager = new ManagerModel();
        if ($mManager->isRepeat('account', $wrapManager->account)) {
            ErrorHandler::setErrorInfo('帐号已经存在！');
            return FALSE;
        }

        $wrapManager->mappingToModel($mManager);
        $mManager->salt = StringUtil::randomString(8);
        $mManager->password = $this->saltPassword($wrapManager->password, $mManager->salt);
        $mManager->status = self::ACCOUNT_STATUS_NORMAL;
        $result = $mManager->create();
        if (!$result) {
            return FALSE;
        }

        $wrapManager->setWrapperProperties($mManager->toArray());
        return $wrapManager;
    }

    public function changePassword($id, $password, $newPassword)
    {

    }
}