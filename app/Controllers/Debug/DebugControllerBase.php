<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/12
 * Time: 16:19
 */

namespace Controllers\Debug;


use Controllers\Admin\AdminControllerBase;
use Controllers\Pub\PubControllerBase;
use Controllers\Priv\PrivControllerBase;
use Core\Base\Controller;
use Core\Tools\Network\Curl;
use Enumerations\DebugConst;

class DebugControllerBase extends Controller
{
    const DEBUG_CLIENT_TYPE = 4;
    const DEBUG_APP_VER = '4.0.1';
    const DEBUG_DEVICE_ID = 'merchants_debug';

    public function initialize()
    {
        $this->view->disable();
        $this->response->setContentType('text/html', 'UTF-8');
    }

    private function printTitle($titleName)
    {
        echo PHP_EOL, '<strong>', $titleName, '</strong>', PHP_EOL;
    }

    private function printContent($content)
    {
        if (is_string($content)) {
            echo $content;
        } elseif (is_bool($content) || is_null($content)) {
            var_dump($content);
        } else {
            print_r($content);
        }
        echo PHP_EOL;
    }


    protected function packageParam($param)
    {
        $postData = array(
            'time' => time(),
            'data' => rawurlencode(json_encode($param, JSON_UNESCAPED_UNICODE)),
            'client_type' => self::DEBUG_CLIENT_TYPE,
            'device_id' => self::DEBUG_DEVICE_ID,
            'app_version' => self::DEBUG_APP_VER,
        );
        switch (DebugConst::MODE) {
            case 'admin':
                $postData['sign'] = AdminControllerBase::createSign($postData, $this->config->secret->clientSignKey);
                break;
            case 'priv':
                $postData['sign'] = PrivControllerBase::createSign($postData, $this->config->secret->clientSignKey);
                break;
            case 'pub':
                $postData['sign'] = PubControllerBase::createSign($postData, $this->config->secret->clientSignKey);
                break;
        }
        return $postData;
    }

    public function postRequest($action, $param)
    {
        $requestData = NULL;
        $url = 'http://' . trim($this->config->self->domain, '/') . '/';
        $url .= $action;
        switch (DebugConst::MODE) {
            case 'admin':
                $requestData = $param;
                break;
            case 'priv':
            case 'pub':
                $requestData = $this->packageParam($param);
                break;
        }
        echo '<pre>';
        $this->printTitle('-------------- REQUEST --------------');
        $this->printTitle('URL:');
        $this->printContent($url);
        $this->printTitle('METHOD:');
        $this->printContent('post');
        $this->printTitle('API PRIVATE DATA:');
        $this->printContent($param);
        $this->printTitle('REQUEST DATA:');
        $this->printContent($requestData);
        $this->printTitle('-------------- RESPONSE --------------');

        $response = Curl::post($url, [], $requestData, NULL, FALSE);

        $this->printTitle('JSON String:');
        $this->printContent($response);
        $this->printTitle('RESPONSE FORMAT:');
        $this->printContent(json_decode($response));
        echo '</pre>';
    }

    public function getRequest($action, $param)
    {
        $url = 'http://' . trim($this->config->self->domain, '/') . '/';
        $url .= $action;
        $requestData = NULL;
        switch (DebugConst::MODE) {
            case 'admin':
                $requestData = $param;
                break;
            case 'priv':
            case 'pub':
                $requestData = $this->packageParam($param);
                break;
        }
        echo '<pre>';
        $this->printTitle('-------------- REQUEST --------------');
        $this->printTitle('URL:');
        $this->printContent($url);
        $this->printTitle('METHOD:');
        $this->printContent('get');
        $this->printTitle('API PRIVATE DATA:');
        $this->printContent($param);
        $this->printTitle('REQUEST DATA:');
        $this->printContent($requestData);
        $this->printTitle('-------------- RESPONSE --------------');
        $response = Curl::get($url, $requestData, NULL, TRUE);
        $this->printTitle('JSON String:');
        $this->printContent($response);
        $this->printTitle('RESPONSE FORMAT:');
        $this->printContent($response);
        echo '</pre>';
    }
}