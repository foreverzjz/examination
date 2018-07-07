<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2018/4/10
 * Time: 下午12:06
 */

namespace Wrappers;

use Core\Base\Wrapper;

class ManagerAuthWrapper extends Wrapper
{
    public $id;
    public $true_name;
    public $power;
    public $role;
    public $loginTime;
    public $mp;
    public $menu;
    public $route;

    private static $_instance;

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