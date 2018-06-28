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
    public $errorMessage;

    public function __construct()
    {
        $this->Di = Di::getDefault();
        $this->config = $this->Di->get('config');
    }
}