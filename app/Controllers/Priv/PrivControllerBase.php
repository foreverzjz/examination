<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/10/11
 * Time: 上午10:09
 */

namespace Controllers\Priv;

use Core\Base\Controller;
use Library\Common;
use Phalcon\Di;
use Wrappers\PrivRequestWrapper;
use Wrappers\PublicRequestWrapper;
use Phalcon\Dispatcher;
use \stdClass;

/**
 * Class PrivControllerBase
 * @property \Core\Tools\MyRedis $_redis;
 * @property \Core\Tools\Validator $validator;
 */

class PrivControllerBase extends Controller
{
    const REQUEST_SIGN_TIMEOUT = 60;

    public function initialize()
    {
//        if (!$this->inWhiteList()) {
//            header('HTTP/1.1 405 Not Allowed');
//            die('Not allowed request.');
//        }

        parent::initialize();
        $this->setJsonResponse();
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
            $paramData['time'] = time();
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
        return $this->input;
    }

    public function beforeExecuteRoute(Dispatcher $dispatcher){
       // $this->allowOrigin();
        $requestData = $this->getRequest();
        if($requestData === FALSE){
            $this->response->setJsonContent(parent::responseError());
            $this->response->send();
            return FALSE;
        }
    }

    /**
     * 判断请求IP是否在白名单
     * @return bool
     */
    protected function inWhiteList()
    {
        $ip = Common::getClientIP(TRUE);
        if (
            ($ip >= 167772161 && $ip <= 167837695)      //10.0.*.*
            ||
            ($ip >= 3232235520 && $ip <= 3232301055)    //192.168.*.*
            ||
            $ip == 2130706433                           //127.0.0.1
            ||
            $ip == 2032909053                           //121.43.186.253
            ||
            $ip == 460525434                            //27.115.15.122
            ||
            $ip == 2362402202                           //140.207.101.154
            ||
            $ip == 1981798741                           //118.31.217.85
            ||
            $ip == 2344942223                           //139.196.250.143
            ||
            $ip == 2346714763                           //139.224.6.139
            ||
            $ip == 460525434
            ||
            $ip == 2130706433
        ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }



    /**
     * 跨域请求许可处理
     */
    protected function allowOrigin()
    {
        $origin = $this->request->getServer('HTTP_ORIGIN');

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