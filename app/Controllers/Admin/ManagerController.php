<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/7/5
 * Time: 上午9:30
 */

namespace Controllers\Admin;

use Business\Manager\AccountBusiness;

class ManagerController extends ManagerControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }

    public function loginAction()
    {
        $this->view->pick("manager/login");
    }
    
    public function authAction()
    {
        parent::setJsonResponse();
        parent::limitRequestMethod('POST');

        $this->validator->validateRequired('account', '帐号');
        $this->validator->validateRequired('password', '密码');

        if (!$this->validator->validateAll($this->request->getPost())) {
            return FALSE;
        }

        $bizManager = new AccountBusiness();
        $result = $bizManager->login($this->request->getPost('account'), $this->request->getPost('password'));
        if (!$result) {
            return FALSE;
        }
        return $result;
    }

    public function indexAction()
    {
        $this->view->pick("manager/index");
    }

    public function logoutAction()
    {
        parent::setJsonResponse();
        $bizManager = new AccountBusiness();
        $bizManager->logout();
        return TRUE;
    }
}