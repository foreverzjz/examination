<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/10/11
 * Time: 上午10:09
 */

namespace Controllers\Pub;

use Core\Base\Controller;
use Core\Tools\ErrorHandler;
use Phalcon\Di;
use Wrappers\PublicRequestWrapper;
use Phalcon\Dispatcher;
use \stdClass;

/**
 * Class PubControllerBase
 * @property \Core\Tools\MyRedis $_redis;
 * @property \Core\Tools\Validator $validator;
 */

class PubControllerBase extends Controller
{
    const REQUEST_SIGN_TIMEOUT = 60;

    public function initialize()
    {
        parent::initialize();
        parent::setJsonResponse();
    }

    /**
     * 创建一个请求参数的签名
     * @param array $paramData
     * @return string
     */
    static Public function createSign(array $paramData = [],string $signKey='')
    {
        $signString = '';
        if (!$paramData['time']) {
            $paramData['time'] == time();
        }
        if (is_array($paramData) && count($paramData) > 0) {
            ksort($paramData);
            foreach ($paramData as $key => $value) {
                $signString .= $value;
            }
        }
        $signString .= $signKey;
        return md5($signString);
    }


    protected function getRequest()
    {
        $serverTime = time();
        $request = [];
        $request['appVersion'] = $this->request->get('app_version');
        $request['clientType'] = (int)$this->request->get('client_type', 'int');
        $request['deviceId'] = $this->request->get('device_id');
        $request['time'] = (int)$this->request->get('time', 'int');
        $request['data'] = $this->request->get('data');
        $requestSign = $this->request->get('sign');

        if (empty($requestSign)) {
            ErrorHandler::setErrorInfo('缺少签名！',ErrorHandler::ERROR_CODE_SIGN_INVALID);
            return FALSE;
        }
        if ($serverTime - $request['time'] >= self::REQUEST_SIGN_TIMEOUT) {
            ErrorHandler::setErrorInfo('签名过期！',ErrorHandler::ERROR_CODE_SIGN_INVALID);
            return FALSE;
        }

        if (self::createSign($request,$this->config->secret->clientSignKey) != $requestSign) {
            ErrorHandler::setErrorInfo('签名错误！',ErrorHandler::ERROR_CODE_SIGN_ERROR);
            return FALSE;
        }
        $wrapRequestData = PublicRequestWrapper::getInstance($request);
        return $wrapRequestData;
    }

    public function beforeExecuteRoute(Dispatcher $dispatcher){
        $requestData = $this->getRequest();
        if($requestData === FALSE){
            $this->response->setJsonContent(parent::responseError());
            $this->response->send();
            return FALSE;
        }
    }

    protected function responseData($data = NULL, int $code = 1)
    {
        $return = new stdClass();
        $return->result =true;
        $return->flag = $code;
        $return->data = $data;
        $return->message = 'Success';
        return $return;
    }

}