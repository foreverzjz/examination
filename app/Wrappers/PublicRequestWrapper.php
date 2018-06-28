<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/10/11
 * Time: 上午11:35
 */

namespace Wrappers;


use Core\Base\Wrapper;

class PublicRequestWrapper extends Wrapper
{
    public $appVersion;
    public $clientType;
    public $deviceId;
    public $time;
    public $data;

    private static $_instance;

    protected function filterSpecChar(string $stringData)
    {
        if (empty($stringData)) {
            return '';
        }
        $requestData = str_replace('%E2%80%AE', '', $stringData);  //过滤RLO
        return $requestData;
    }

    public function setTime($value)
    {
        $this->time = (int)$value;
    }

    public function setClientType($value)
    {
        $this->clientType = (int)$value;
    }

    public function setData(string $value)
    {
        $this->data = json_decode(urldecode($this->filterSpecChar($value)));
    }

    public function getDataArray()
    {
        return !empty($this->data)?get_object_vars($this->data):[];
    }

    private function __construct(array $data = NULL)
    {
        if (is_array($data)) {
            parent::setWrapperProperties($data);
        }
    }

    public static function getInstance(array $data = NULL)
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($data);
        }
        return self::$_instance;
    }
}