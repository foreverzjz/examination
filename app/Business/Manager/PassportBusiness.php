<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2018/4/10
 * Time: 下午6:05
 */

namespace Business\Manager;

use Core\Base\Business;
use Phalcon\Di;
use Wrappers\ManageAuthWrapper;

class ___PassportBusiness extends Business
{
    const AUTH_SESSION_KEY = 'auth_manager';

    protected function setAuth(ManageAuthWrapper $auth)
    {
        parent::getSession();
        $this->session->set(self::AUTH_SESSION_KEY, $auth->toArray());
    }


    protected function login(string $account, string $password)
    {
        try{
            $mManager = Manager::findFirst("mp = '{$account}' OR account = '{$account}'");
        }catch (Exception $exception){
            var_dump($exception);
        }

        if (!$mManager) {
            $this->setErrorInfo('用户名或密码错误！');
            return FALSE;
        }
        if ($mManager->password != Common::saltPassword($password, $mManager->salt)) {
            $this->setErrorInfo('用户名或登录密码错误！');
            return FALSE;
        }
        $this->cancelAuth($mManager->id);

        $loginTime = date('Y-m-d H:i:s');

        $mManagerSign = new ManagerSign();
        $mManagerSign->manager_id = $mManager->id;
        $mManagerSign->sign_ip = Common::getClientIP(FALSE);
        $mManagerSign->sign_channel_id = $mManager->channel_id;
        $mManagerSign->sign_roles = $mManager->roles;
        $mManagerSign->session_id = $this->session->getId();
        $mManagerSign->create_at = $loginTime;
        $result = $mManagerSign->save();
        if (!$result) {
            $this->setErrorInfo('登录失败！');
            return FALSE;
        }


        if ($mManager->channel_id == 0 && ($mManager->roles == 'administrator' || $mManager->roles == 'global_data_view')) {
            $mManager->channel_id = ChannelBusiness::DEFAULT_CHANNEL;
        }
        $auth = $mManager->toArray();
        $auth['channel'] = ChannelBusiness::getChannelName($mManager->channel_id);
        $auth['allow_shop_ids'] = ShopBusiness::getChannelsShopIds($mManager->channel_id);
        $this->setAuth($auth, $loginTime);
        return TRUE;
    }

    protected function logout()
    {
        $this->session->remove(self::AUTH_SESSION_KEY);
    }
}