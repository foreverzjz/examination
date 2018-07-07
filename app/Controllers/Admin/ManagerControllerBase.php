<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/7/5
 * Time: 上午9:42
 */

namespace Controllers\Admin;

use Business\Manager\AccountBusiness;
use Core\Base\Controller;

class ManagerControllerBase extends Controller
{
    protected $auth;
    protected $whiteList = [
        '/admin/manager/login',
        '/admin/manager/auth'
    ];
    protected $redirectIndex = [
        '/admin/manager/login'
    ];
    public function initialize()
    {
        parent::initialize();
        $this->auth = AccountBusiness::getAuth();
        $this->checkAuth();
        $this->view->setViewsDir(VIEWS_PATH . 'Manage');
    }

    public function checkAuth()
    {
        $requestUri = $this->request->getURI();
        if(empty($this->auth) && !in_array($requestUri, $this->whiteList)){
            $this->redirect("/admin/manager/login");
        }
        if(!empty($this->auth) && in_array($requestUri, $this->redirectIndex)){
            $this->redirect("/admin/manager/index");
        }
    }
}