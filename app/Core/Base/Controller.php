<?php
/**
 * 控制器基类
 * 项目下所有控制器必须继承此类
 * @property array $input;
 * @property \Core\Tools\Validator $validator
 * @property \Core\Tools\MyRedis $redis;
 */

namespace Core\Base;

use Core\Tools\ErrorHandler;
use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Mvc\Dispatcher;
use \stdClass;
use Core\Tools\SeasLogPlugin;

/**
 * Class ControllerApi
 * @property \Log\Log $log
 * @property stdClass $config;
 */

class Controller extends PhalconController
{
    protected $_input = [];
    protected $_auth = [];
    private $_isJsonResponse;
    private $_isCheckOrigin = FALSE;

    public function initialize()
    {
    }

    protected function setCheckOrigin()
    {
        $this->_isCheckOrigin = TRUE;
    }

    protected function setJsonResponse()
    {
        $this->_isJsonResponse = TRUE;
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
    }

    protected function saveAccessLog($response)
    {
        $post = !empty($this->request->getPost())?$this->request->getPost():$this->request->getJsonRawBody();
        SeasLogPlugin::accessLog($post, $this->request->getQuery(), $response);
    }


    protected function saveErrorLog($message, $data = [])
    {
        $param = [
            'POST' => $this->request->getPort(),
            'GET' => $this->request->get(),
            'DATA' => $data
        ];

        SeasLogPlugin::error($message, str_replace('\\', DS, get_called_class()), $param);
    }

    protected function redirect($redirect)
    {
        $this->response->redirect($redirect);
        $this->response->send();
    }


    protected function responseError(string $message = NULL, int $flag = -1, $data = NULL)
    {
        if(!empty(ErrorHandler::$message)) {
            $message = ErrorHandler::$message;
        }
        if ($flag == -1) {
            $flag = ErrorHandler::$flag;
        }
        $return = new stdClass();
        $return->result = FALSE;
        $return->flag = $flag;
        $return->message = $message;
        if(!empty($data)) {
            $return->data = $data;
        }
        return $return;
    }

    protected function responseData($data = NULL, int $flag = 1)
    {
        $return = new stdClass();
        $return->result = TRUE;
        $return->flag = $flag;
        $return->data = $data;
        $return->message = 'Success';
        return $return;
    }

    /**
     * 跨域请求许可处理
     */
    protected function allowOrigin()
    {
        $origin = $this->request->getServer('HTTP_ORIGIN');

        if(empty($origin)){
            header('HTTP/1.1 405 Not Allowed');
            die('No \'Access-Control-Allow-Origin\' header is present on the requested resource.Origin is therefore not allowed access.');
        }

        $allowOrigin = [
            'imcoming.com',
            'imcoming.com.cn',
            'imcoming.cn',
            'anlaiye.com',
            'anlaiye.com.cn',
            'imcome.net',
        ];


        $matches = [];
        preg_match('/[\w][\w-]*\.(?:com\.cn|com|cn|net)(\/|$)/isU', explode(':',$origin)[1], $matches);
        $domain = trim($matches[0], '/');

        if (!empty($origin) && in_array($domain, $allowOrigin)) {
            header("Access-Control-Allow-Origin:{$origin}");
        } else {
            header('HTTP/1.1 405 Not Allowed');
            die('No \'Access-Control-Allow-Origin\' header is present on the requested resource.Origin is therefore not allowed access.');
        }
        //header('Access-Control-Allow-Origin:*');
    }


    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
        if ($this->_isCheckOrigin == TRUE) {
            $this->allowOrigin();
        }
        $data = $dispatcher->getReturnedValue();
        if ($this->_isJsonResponse) {
            $this->response->setJsonContent($data);
            $this->response->send();
        } else {
            //输出公共变量到volt
        }
        $this->saveAccessLog($data);

    }
}
