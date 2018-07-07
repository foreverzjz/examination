<?php
/**
 * Created by PhpStorm.
 * User: Peter Pan
 * Date: 2016/10/12
 * Time: 22:47
 */

namespace Core\Base;

use Phalcon\Di;

/**
 * Class ControllerApi
 * @property \Log\Log $log
 */
class Business
{
    protected $Di;
    protected $config;
    protected $redis;
    protected $session;
    protected $cookies;

    public function __construct()
    {
        $this->Di = Di::getDefault();
        $this->config = $this->Di->get('config');
    }

    public function getRedis()
    {
        $this->redis = $this->Di->get('redis');
    }

    public function getSession()
    {
        $this->session = $this->Di->get('session');
    }

    public function getCookies()
    {
        $this->cookies = $this->Di->get('cookies');
    }
}