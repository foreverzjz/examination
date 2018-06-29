<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/6/29
 * Time: 上午11:24
 */

namespace Controllers;
use Business\PassportBusiness;

/**
 * 验证类
 * Class PassportController
 * @property \Core\Tools\Validator $validator
 * @package Controllers
 */
class PassportController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function loginAction()
    {
        $this->limitRequestMethod('POST');

        $requestData = $this->getRequest();
        $this->validator->validateRequired('username', '账号');
        $this->validator->validateRequired('password', '密码');
        if(!$this->validator->validateAll($requestData)){
            return $this->responseError();
        }

        $bPassport = new PassportBusiness();
        $result = $bPassport->login($requestData['username'], $requestData['password']);
        if($result === false){
            return $this->responseError();
        }

        return $this->responseData($result);
    }
}