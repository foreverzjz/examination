<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/10/11
 * Time: 上午10:09
 */

namespace Controllers\Admin;

use Core\Base\Controller;
use Phalcon\Di;
use Phalcon\Dispatcher;
use \stdClass;
use Wrappers\AdminRequestWrapper;
/**
 * Class AdminControllerBase
 */

class AdminControllerBase extends Controller
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
        return true;
    }


    protected function getRequest()
    {
        return $this->input;
    }

    public function beforeExecuteRoute(Dispatcher $dispatcher){
//        $this->allowOrigin();
        header("Access-Control-Allow-Origin:*");
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

    /**
     * 跨域请求许可处理
     */
    protected function allowOrigin()
    {
        $origin = $this->request->getServer('HTTP_ORIGIN');
        $host = $this->request->getHttpHost();
        $host = explode('.',$host);
        $allowHost = [
            'manager-center',
        ];
        if (!empty($host) && in_array($host['0'], $allowHost)) {
            header("Access-Control-Allow-Origin:{$origin}");

        }else{
            $allowOrigin = [
                'imcoming.com',
                'imcoming.com.cn',
                'imcoming.cn',
                'anlaiye.com',
                'anlaiye.com.cn',
                'imcome.net',
            ];

            $matches = [];
            preg_match('/[\w][\w-]*\.(?:com\.cn|com|cn|net)(\/|$)/isU', $origin, $matches);
            $domain = trim($matches[0],'/');

            if (!empty($origin) && in_array($domain, $allowOrigin)) {
                header("Access-Control-Allow-Origin:{$origin}");
               
            }else{
                header('HTTP/1.1 405 Not Allowed');
                die('No \'Access-Control-Allow-Origin\' header is present on the requested resource.Origin is therefore not allowed access.');
            }
        }
    }

    /**
     * 处理权限字段
     * @param $data
     * @return mixed
     */
    public function dealData($data)
    {
        $data['extra'] = is_array($data['extra'])?$data['extra']:json_decode($data['extra'],TRUE);
        if(!empty($data['extra']['school_id']) && empty($data['extra']['city_id']))
        {
            $data['identity'] = 1;
        }
        if(!empty($data['extra']['school_id'])) {
            $data['school_id'] = $data['extra']['school_id'];
        }
        if(!empty($data['extra']['shop_id'])) {
            $data['shop_id'] = $data['extra']['shop_id'];
        }
        if(!empty($data['extra']['city_id'])) {
            $data['city_id'] = $data['extra']['city_id'];
        }
        if(!empty($data['extra']['business_code'])){
            $data['business_code'] = $data['extra']['business_code'];
        }
        unset($data['extra']);
        return $data;
    }
}